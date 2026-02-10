<?php

namespace Ae3\LaravelLogsLayer\Tests\Unit;

use Ae3\LaravelLogsLayer\app\DataTransferObjects\ExceptionContextDTO;
use Ae3\LaravelLogsLayer\app\Exceptions\InvalidArgumentException;
use Ae3\LaravelLogsLayer\app\Services\Contracts\LogServiceInterface;
use Ae3\LaravelLogsLayer\app\Services\DailyLogService;
use Ae3\LaravelLogsLayer\app\Services\DiscordLogService;
use Ae3\LaravelLogsLayer\app\Services\EmailLogService;
use Ae3\LaravelLogsLayer\app\Services\LogstashLogService;
use Ae3\LaravelLogsLayer\app\Traits\LogTrait;
use Ae3\LaravelLogsLayer\Tests\Constraints\InstanceOfAtLeastOne;
use Ae3\LaravelLogsLayer\Tests\TestCase;
use Exception;
use Mockery;
use ReflectionException;

class LogTraitTest extends TestCase
{
    /**
     * Cria uma instância concreta do trait para testes
     * Substitui getMockForTrait() que está deprecado no PHPUnit 10+
     */
    private function createTraitInstance(): object
    {
        return new class {
            use LogTrait;

            public function initializeLogServices(): void
            {
                parent::initializeLogServices();
            }

            public function getLogServices(): array
            {
                return $this->logServices;
            }

            public function logInfo(string $message, array $customData = []): void
            {
                parent::logInfo($message, $customData);
            }

            public function logNotice(string $message, array $customData = []): void
            {
                parent::logNotice($message, $customData);
            }

            public function logWarning(string $message, array $customData = []): void
            {
                parent::logWarning($message, $customData);
            }

            public function logDebug(string $message, array $customData = []): void
            {
                parent::logDebug($message, $customData);
            }

            public function logAlert(string $message, array $customData = []): void
            {
                parent::logAlert($message, $customData);
            }

            public function logException(string $caller, \Throwable $exception, string $log_level = 'error', array $customData = []): \Ae3\LaravelLogsLayer\app\DataTransferObjects\LoggedExceptionDTO
            {
                return parent::logException($caller, $exception, $log_level, $customData);
            }
        };
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testLogServiceReturnsLogstashServiceWhenDefaultIsLogstash(): void
    {
        config(['logging.default' => 'logstash']);
        config(['logging.channels.stack.channels' => []]);

        $traitInstance = $this->createTraitInstance();
        $traitInstance->initializeLogServices();

        $this->assertContainsOnlyInstancesOf(LogstashLogService::class, $traitInstance->getLogServices());
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testLogServiceReturnsDailyLogServiceWhenDefaultIsDaily(): void
    {
        config(['logging.default' => 'daily']);
        config(['logging.channels.stack.channels' => []]);

        $traitInstance = $this->createTraitInstance();
        $traitInstance->initializeLogServices();

        $this->assertContainsOnlyInstancesOf(DailyLogService::class, $traitInstance->getLogServices());
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testLogServiceReturnsDiscordLogServiceWhenDefaultIsDiscord(): void
    {
        config(['logging.default' => 'discord']);
        config(['logging.channels.stack.channels' => []]);

        $traitInstance = $this->createTraitInstance();
        $traitInstance->initializeLogServices();

        $this->assertContainsOnlyInstancesOf(DiscordLogService::class, $traitInstance->getLogServices());
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testLogServiceReturnsEmailLogServiceWhenDefaultIsEmail(): void
    {
        config(['logging.default' => 'email']);
        config(['logging.channels.stack.channels' => []]);

        $traitInstance = $this->createTraitInstance();
        $traitInstance->initializeLogServices();

        $this->assertContainsOnlyInstancesOf(EmailLogService::class, $traitInstance->getLogServices());
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testLogServiceReturnsAllLogServicesWhenDefaultIsStack(): void
    {
        config(['logging.default' => 'stack']);
        config(['logging.channels.stack.channels' => ['logstash', 'daily', 'discord', 'email']]);

        $traitInstance = $this->createTraitInstance();
        $traitInstance->initializeLogServices();

        $this->assertContainsInstanceOf(LogstashLogService::class, $traitInstance->getLogServices());
        $this->assertContainsInstanceOf(DailyLogService::class, $traitInstance->getLogServices());
        $this->assertContainsInstanceOf(DiscordLogService::class, $traitInstance->getLogServices());
        $this->assertContainsInstanceOf(EmailLogService::class, $traitInstance->getLogServices());
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testLogServiceReturnsOnlyLogstashLogServiceWhenStackContainsOnlyLogstash(): void
    {
        config(['logging.default' => 'stack']);
        config(['logging.channels.stack.channels' => ['logstash']]);

        $traitInstance = $this->createTraitInstance();
        $traitInstance->initializeLogServices();

        $this->assertContainsOnlyInstancesOf(LogstashLogService::class, $traitInstance->getLogServices());
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testLogServiceReturnsOnlyDailyLogServiceWhenStackContainsOnlyDaily(): void
    {
        config(['logging.default' => 'stack']);
        config(['logging.channels.stack.channels' => ['daily']]);

        $traitInstance = $this->createTraitInstance();
        $traitInstance->initializeLogServices();

        $this->assertContainsOnlyInstancesOf(DailyLogService::class, $traitInstance->getLogServices());
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testLogServiceReturnsOnlyDiscordLogServiceWhenStackContainsOnlyDiscord(): void
    {
        config(['logging.default' => 'stack']);
        config(['logging.channels.stack.channels' => ['discord']]);

        $traitInstance = $this->createTraitInstance();
        $traitInstance->initializeLogServices();

        $this->assertContainsOnlyInstancesOf(DiscordLogService::class, $traitInstance->getLogServices());
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testLogServiceReturnsOnlyEmailLogServiceWhenStackContainsOnlyEmail(): void
    {
        config(['logging.default' => 'stack']);
        config(['logging.channels.stack.channels' => ['email']]);

        $traitInstance = $this->createTraitInstance();
        $traitInstance->initializeLogServices();

        $this->assertContainsOnlyInstancesOf(EmailLogService::class, $traitInstance->getLogServices());
    }


    /**
     * @return void
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function testLogExceptionWithInvalidLogLevelThrowsInvalidArgumentException(): void
    {
        config(['logging.default' => 'discord']);
        config(['logging.channels.stack.channels' => ['discord']]);

        $traitInstance = $this->createTraitInstance();

        $this->expectException(InvalidArgumentException::class);
        $traitInstance->logException('TestCaller', new Exception('Test exception'), 'invalid_log_level');
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function testLogExceptionWithValidLogLevel(): void
    {
        $traitInstance = $this->createTraitInstance();
        $mockLogService = Mockery::mock(LogServiceInterface::class);
        app()->instance(LogServiceInterface::class, $mockLogService);

        $mockException = new Exception('Test exception');
        $mockLogContextDTO = ExceptionContextDTO::fromArray([
            'root_namespace' => 'mocked_namespace',
            'tags' => ['tag1', 'tag2'],
            'current_user' => (object)['id' => 1, 'name' => 'Tobias'],
            'current_url' => 'https://example.com',
            'custom_data' => ['key' => 'value'],
        ]);

        $mockLogService->shouldReceive('error')
            ->withArgs(function ($caller, $exception, $context) use ($mockLogContextDTO) {
                return $caller === 'TestCaller'
                    && $exception instanceof Exception
                    && $context == $mockLogContextDTO;
            });

        $result = $traitInstance->logException('TestCaller', $mockException, 'error');

        $this->assertStringContainsString('Error code:', $result->message);
        $this->assertLogFileContains($result->code);
    }

    /**
     * Testa se o arquivo de log contém o conteúdo esperado
     *
     * @param string $expectedContent
     * @return void
     */
    private function assertLogFileContains(string $expectedContent): void
    {
        // Determinando o caminho do arquivo de log
        $logFilename = 'laravel-' . now()->format('Y-m-d') . '.log';
        $logFilePath = storage_path('logs/' . $logFilename);

        // Garantindo que o arquivo de log existe
        $this->assertTrue(file_exists($logFilePath), "O arquivo de log não existe: $logFilePath");

        // Lendo o conteúdo do arquivo de log
        $logContent = file_get_contents($logFilePath);

        // Verificando se o conteúdo esperado está presente no arquivo
        $this->assertStringContainsString($expectedContent, $logContent);
    }

    /**
     * @param string $className
     * @param iterable $haystack
     * @param string $message
     * @return void
     */
    public static function assertContainsInstanceOf(string $className, iterable $haystack, string $message = ''): void
    {
        static::assertThat(
            $haystack,
            new InstanceOfAtLeastOne($className),
            $message
        );
    }
}