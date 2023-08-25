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
use Ae3\LaravelLogsLayer\Tests\TestCase;
use Exception;
use Mockery;
use ReflectionException;

class LogTraitTest extends TestCase
{
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
        config(['logging.channels.stack.channels' => ['logstash']]);

        $traitInstance = $this->getMockForTrait(LogTrait::class);
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
        config(['logging.channels.stack.channels' => ['daily']]);

        $traitInstance = $this->getMockForTrait(LogTrait::class);
        $traitInstance->initializeLogServices();

        $this->assertContainsOnlyInstancesOf(DailyLogService::class, $traitInstance->getLogServices());
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testLogServiceReturnsDiscordLogServiceWhenDefaultIsDaily(): void
    {
        config(['logging.default' => 'discord']);
        config(['logging.channels.stack.channels' => ['discord']]);

        $traitInstance = $this->getMockForTrait(LogTrait::class);
        $traitInstance->initializeLogServices();

        $this->assertContainsOnlyInstancesOf(DiscordLogService::class, $traitInstance->getLogServices());
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testLogServiceReturnsEmailLogServiceWhenDefaultIsDaily(): void
    {
        config(['logging.default' => 'email']);
        config(['logging.channels.stack.channels' => ['email']]);

        $traitInstance = $this->getMockForTrait(LogTrait::class);
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

        $traitInstance = $this->getMockForTrait(LogTrait::class);

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
        $traitInstance = $this->getMockForTrait(LogTrait::class);
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
}