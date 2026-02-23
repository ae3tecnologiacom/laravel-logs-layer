<?php

namespace Ae3\LaravelLogsLayer\app\Services;

use Ae3\LaravelLogsLayer\app\Containers\LogDataContainer;
use Ae3\LaravelLogsLayer\app\DataTransferObjects\ExceptionContextDTO;
use Ae3\LaravelLogsLayer\app\Jobs\ProcessLog;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class AbstractLogService implements Contracts\LogServiceInterface
{

    /**
     * @var LogDataContainer
     */
    private LogDataContainer $logDataContainer;

    /**
     * @param LogDataContainer $logDataContainer
     */
    public function __construct(LogDataContainer $logDataContainer)
    {
        $this->logDataContainer = $logDataContainer;
    }

    /**
     * @return string
     */
    abstract protected function getLogChannel(): string;

    /**
     * O nível de log emergency é o mais alto em termos de gravidade. Ele deve ser usado para situações em que ocorrem erros graves que requerem intervenção imediata, uma vez que podem representar uma interrupção crítica ou completa do funcionamento da aplicação.
     * Exemplos incluem falhas graves de infraestrutura, perda de conexão com bancos de dados essenciais, falhas de segurança críticas, entre outros.
     * @param string $caller
     * @param Throwable $exception
     * @param ExceptionContextDTO $contextDto
     * @return void
     */
    public function emergency(string $caller, Throwable $exception, ExceptionContextDTO $contextDto): void
    {
        $logData = $this->buildLogData($caller, $exception, $contextDto);
        $this->log('emergency', $exception->getMessage(), $logData);
    }

    /**
     * O nível critical é usado para erros críticos que também requerem atenção urgente, embora possam não ser tão catastróficos quanto os eventos de emergência. Esses erros têm um impacto significativo na operação da aplicação e exigem uma investigação e correção imediatas.
     * Um exemplo de uso seria quando uma funcionalidade vital da aplicação falha, mas a aplicação ainda consegue continuar operando em um modo limitado.
     * @param string $caller
     * @param Throwable $exception
     * @param ExceptionContextDTO $contextDto
     * @return void
     */
    public function critical(string $caller, Throwable $exception, ExceptionContextDTO $contextDto): void
    {
        $logData = $this->buildLogData($caller, $exception, $contextDto);
        $this->log('critical', $exception->getMessage(), $logData);
    }

    /**
     * O nível error é um nível de gravidade menor em comparação com os anteriores. Ele é usado para registrar erros que ocorrem na aplicação, mas não são tão críticos a ponto de interromper completamente o funcionamento da aplicação. Erros desse tipo não impedem a aplicação de continuar operando, mas ainda assim precisam ser corrigidos para evitar problemas futuros ou impactos negativos nos usuários.
     * Exemplos podem incluir erros de validação de entrada, erros de banco de dados não críticos, falhas de autenticação, entre outros.
     * @param string $caller
     * @param Throwable $exception
     * @param ExceptionContextDTO $contextDto
     * @return void
     */
    public function error(string $caller, Throwable $exception, ExceptionContextDTO $contextDto): void
    {
        $logData = $this->buildLogData($caller, $exception, $contextDto);
        $this->log('error', $exception->getMessage(), $logData);
    }

    /**
     * O nível de log warning é usado para registrar situações em que ocorrem problemas potenciais ou indesejados que não são erros graves, mas merecem atenção. Warnings indicam que algo não está funcionando exatamente como o esperado, mas a aplicação ainda é capaz de continuar operando.
     * Um exemplo pode ser a depreciação de uma funcionalidade que será removida em futuras versões da aplicação.
     * @param string $message
     * @param array $messageContext
     * @return void
     */
    public function warning(string $message, array $messageContext = []): void
    {
        $logData = $this->buildGenericLogData($message, $messageContext);
        $this->log('warning', $message, $logData);
    }

    /**
     * O nível notice é usado para registrar informações importantes que não são consideradas problemas. Esse nível é mais informativo e serve para fornecer insights sobre eventos relevantes na aplicação. Ele é usado para indicar eventos significativos que podem ajudar na depuração e monitoramento da aplicação, mas que não são necessariamente problemas.
     * @param string $message
     * @param array $messageContext
     * @return void
     */
    public function notice(string $message, array $messageContext = []): void
    {
        $logData = $this->buildGenericLogData($message, $messageContext);
        $this->log('notice', $message, $logData);
    }

    /**
     * O nível info é usado para registrar informações gerais sobre o funcionamento da aplicação. Ele pode ser usado para registrar eventos normais e ações realizadas pela aplicação, permitindo que você acompanhe o fluxo de execução e atividades relevantes.
     * @param string $message
     * @param array $messageContext
     * @return void
     */
    public function info(string $message, array $messageContext = []): void
    {
        $logData = $this->buildGenericLogData($message, $messageContext);
        $this->log('info', $message, $logData);
    }

    /**
     * O nível debug é usado para registros de depuração. Ele é usado para registrar informações detalhadas sobre o fluxo de execução da aplicação, variáveis, valores e outras informações úteis para diagnóstico durante o desenvolvimento. Registros de nível debug geralmente são úteis apenas para desenvolvedores e podem ser desativados em ambientes de produção.
     * @param string $message
     * @param array $messageContext
     * @return void
     */
    public function debug(string $message, array $messageContext = []): void
    {
        $logData = $this->buildGenericLogData($message, $messageContext);
        $this->log('debug', $message, $logData);
    }

    /**
     * O nível alert é menos comum e muitas vezes não é usado diretamente. Ele é reservado para situações em que uma ação imediata é necessária, mas que não são tão críticas quanto eventos de emergência ou críticos. Normalmente, um evento de nível alert indicaria uma condição que exige atenção, mas não é uma falha crítica que interrompe a aplicação.
     * @param string $message
     * @param array $messageContext
     * @return void
     */
    public function alert(string $message, array $messageContext = []): void
    {
        $logData = $this->buildGenericLogData($message, $messageContext);
        $this->log('alert', $message, $logData);
    }

    /**
     * @param string $caller
     * @param Throwable $exception
     * @param ExceptionContextDTO $contextDto
     * @return array
     */
    protected function buildLogData(string $caller, Throwable $exception, ExceptionContextDTO $contextDto): array
    {
        return [
            'caller' => $caller,
            'status_code' => $exception->getCode(),
            'line' => $exception->getLine(),
            'file' => $exception->getFile(),
            'error_code' => $contextDto->code,
            'custom_data' => $this->asPrettyJson($contextDto->custom_data),
            'tags' => $contextDto->tags,
            'exception' => get_class($exception),
            'current_url' => $contextDto->current_url,
            'current_user' => $this->asPrettyJson($contextDto->current_user),
            'stack_trace' => $exception->getTraceAsString(),
            'classes' => $this->asPrettyJson($this->getContextClasses($exception->getTrace(), $contextDto->root_namespace)),
            'queries' => $this->asPrettyJson($this->logDataContainer->getCapturedQueries()),
            'guzzle' => $this->asPrettyJson($this->logDataContainer->getCapturedHttpClientEvents()),
        ];
    }

    /**
     * @param string $message
     * @param array $context
     * @return array
     */
    protected function buildGenericLogData(string $message, array $context): array
    {
        return [
            'message' => $message,
            'custom_data' => $context['custom_data'],
            'current_url' => $context['current_url'],
            'current_user' => $this->asPrettyJson($context['current_user']),
            'tags' => $context['tags'],
            'queries' => $this->asPrettyJson($this->logDataContainer->getCapturedQueries()),
            'guzzle' => $this->asPrettyJson($this->logDataContainer->getCapturedHttpClientEvents()),
        ];
    }

    /**
     * @param array $stack_trace
     * @param string $namespace
     * @return array
     */
    protected function getContextClasses(array $stack_trace, string $namespace): array
    {
        $context_classes = [];
        foreach ($stack_trace as $trace) {
            if (isset($trace['class']) && strpos($trace['class'], $namespace) === 0) {
                $context_classes[] = $trace['class'];
            }
        }
        return array_unique($context_classes);
    }

    /**
     * @param $data
     * @return false|string
     */
    protected function asPrettyJson($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $data
     * @return void
     */
    protected function log(string $level, string $message, array $data): void
    {
        // Adiciona o campo level no primeiro nível para compatibilidade com Logstash
        $data['level'] = strtoupper($level);

        Log::channel($this->getLogChannel())->$level($message, $data);
    }
}