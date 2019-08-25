<?php

namespace Features;

use App\Entity\User\User;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Exception;
use Features\Service\Auth;
use Features\Service\Http;
use Features\Service\Json;
use Features\Service\Loader;
use Features\Service\Storage;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Yaml;

class BaseContext implements KernelAwareContext
{
    use KernelDictionary;

    /** @var Storage  */
    private $storage;

    /** @var Http */
    private $http;

    /** @var Loader */
    private $loader;

    /** @var Auth */
    private $auth;

    /** @var Response|null */
    private $lastResponse;

    /** @var Json|null */
    private $lastJsonContent;

    public function __construct(Storage $storage, Http $http)
    {
        $this->storage = $storage;
        $this->http = $http;
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario()
    {
        $this->initializeServices();

        $this->lastResponse = null;
        $this->loader->start();
    }

    /**
     * @AfterScenario
     */
    public function afterScenario()
    {
        $this->loader->finish();
    }

    /**
     * @Given I am as :alias
     *
     * @param string $alias
     * @throws Exception
     */
    public function iAmAs($alias)
    {
        /** @var UserInterface $user */
        $user = $this->loader->getManager()->find(User::class, $this->storage->get($alias));
        if (!$user instanceof UserInterface)
            throw new Exception(sprintf('User "%s" not found!', $alias));

        $this->storage->set('token', $this->auth->createToken($user));
    }

    /**
     * @When I send a :method request to :url
     *
     * @param string $method
     * @param string $url
     * @param PyStringNode|null $body
     * @param array $files
     * @throws Exception
     */
    public function sendRequestTo($method, $url, PyStringNode $body = null, $files = [])
    {
        $request = $this->http->makeRequest(
            $method,
            $url,
            [],
            $body !== null ? $body->getRaw() : null,
            $files
        );

        $this->lastResponse = $this->getKernel()->handle($request);
        $this->lastJsonContent = null;
    }

    /**
     * @Then /^Response code: ([^ ]*)$/
     *
     * @param int $expectedCode
     */
    public function responseCodeShouldBe($expectedCode)
    {
        $actualCode = $this->lastResponse ?
            $this->lastResponse->getStatusCode() : 0;

        if ($actualCode !== $expectedCode) {
            Assert::assertEquals($expectedCode, $actualCode, sprintf(
                "Expected status code '%s', instead '%s' given. \n Response content: \n\r\t %s \n\r\t",
                $expectedCode, $actualCode, $this->lastResponse ? $this->lastResponse->getContent() : ''
            ));
        }
    }

    /**
     * @Then the JSON node :node should be equals to :value
     *
     * @param string $node
     * @param string $value
     * @throws Exception
     */
    public function theJsonNodeShouldBeEqTo($node, $value)
    {
        $node = $this->storage->formatKeyAsValue($node);
        $value = $this->storage->formatKeyAsValue($value);

        if (strtolower($value) === '~any~')
            return;

        try {
            $actual = $this->getJson()->getValue($node);
        } catch (Exception $e) {
            if ($value === '') {
                return;
            }
            throw $e;
        }

        if (!($value === $actual || $value === json_encode($actual))) {
            Assert::assertEquals($value, json_encode($actual));
        }
    }

    /**
     * @Then the JSON node :node should have :count element
     *
     * @param string $node
     * @param string $count
     * @throws Exception
     */
    public function theJsonNodeShouldHaveElements($node, $count)
    {
        $node = $this->storage->formatKeyAsValue($node);
        $count = $this->storage->formatKeyAsValue($count);

        try {
            $actual = $this->getJson()->getValue($node);
        } catch (Exception $e) {
            if ($count === '') {
                return;
            }
            throw $e;
        }

        Assert::assertEquals($count, count((array) $actual));
    }

    /**
     * @Then I save node :node to :paramName
     *
     * @param string $node
     * @param string $paramName
     * @throws Exception
     */
    public function saveNodeToParam($node, $paramName)
    {
        $value = $this->getJson()->getValue($node);
        $this->storage->set('{{' . trim($paramName, '{}') . '}}', $value);
    }

    /**
     * @Then the JSON node :node should be valid according to the schema :filename
     *
     * @param string $node
     * @param string $filename
     * @throws Exception
     */
    public function theJsonShouldBeValidAccordingToTheSchema($node, $filename)
    {
        $filename = __DIR__.'/../../schemas/' . $filename;
        $this->checkFile($filename, 'The JSON schema doesn\'t exist');

        $json = new Json($this->getJson()->getValue($node));
        $json->validate('file://'.realpath($filename));
    }

    /**
     * @Then print last JSON response
     *
     * @throws Exception
     */
    public function printLastJsonResponse()
    {
        echo $this->getJson()->encode();
    }

    /**
     * @Given the following fixtures are loaded:
     *
     * @param TableNode $fixtures
     * @param bool $once
     * @throws Exception
     */
    public function thereAreSeveralFixtures(TableNode $fixtures, bool $once = false)
    {
        $fixtureFiles = [];
        foreach ($fixtures->getRows() as $row) {
            $fileName = $row[0];
            $filePath = __DIR__.'/../../fixtures/' . $fileName;
            $this->checkFile($filePath, sprintf('The YAML fixture "%s" doesn\'t exist', $fileName));
            $value = Yaml::parse((string)file_get_contents($filePath));
            if (array_key_exists('parameters', $value) && is_array($value['parameters'])) {
                foreach ($value['parameters'] as $key => $value) {
                    $this->storage->set($key, $value);
                }
            }
            $fixtureFiles[] = $filePath;
        }
        $this->loader->loadFixtures($fixtureFiles, $once);
    }

    /**
     * @Given first the following fixtures are loaded:
     *
     * @param TableNode $fixtures
     * @throws Exception
     */
    public function firstThereAreSeveralFixtures(TableNode $fixtures)
    {
        $this->thereAreSeveralFixtures($fixtures, true);
    }

    /**
     * @Given run next in transaction
     */
    public function runNextInTransaction()
    {
        $this->loader->startTransaction();
    }

    /**
     * @Given run dql :query
     *
     * @param string $query
     */
    public function runDqlQuery($query)
    {
        $this->loader->getManager()->createQuery($query)->execute();
    }

    /**
     * @Given clear manager
     */
    public function clearManager()
    {
        $this->loader->clear();
    }

    /**
     * @return Json
     * @throws Exception
     */
    private function getJson(): Json
    {
        return is_null($this->lastJsonContent)
            ? $this->lastJsonContent = new Json($this->lastResponse ? $this->lastResponse->getContent() : null)
            : $this->lastJsonContent;
    }

    /**
     * @param string $filename
     * @param string $message
     * @throws Exception
     */
    private function checkFile(string $filename, string $message)
    {
        if (false === is_file($filename)) {
            throw new Exception($message);
        }
    }

    private function initializeServices()
    {
        if (!$this->loader instanceof Loader) {
            $this->loader = new Loader(
                $this->getContainer()->get('doctrine.orm.entity_manager'),
                $this->getContainer()->get('fidry_alice_data_fixtures.loader.doctrine'),
                $this->storage
            );
        }

        if (!$this->auth instanceof Auth) {
            $this->auth = new Auth(
                $this->getContainer()->get('lexik_jwt_authentication.jwt_manager'),
                $this->storage
            );
        }
    }
}
