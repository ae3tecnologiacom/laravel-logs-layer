## LaravelLogsLayer - Captura de Logs de Erro

A biblioteca LaravelLogsLayer permite capturar logs de erros em aplicações Laravel e redirecioná-los para diversos canais, como Discord, E-mail, Logstash e Daily. Essa funcionalidade ajuda no monitoramento e notificação imediata de problemas em ambientes de produção.

### Instalação

Para começar a usar esta lib, siga estas etapas:

1. Adicione o repositório da lib ao composer.json de sua aplicação:

```json
 "repositories": [
    // Outros repositórios,
    {
        "type": "git",
        "url": "https://git.ae3tecnologia.com.br/AE3_TECNOLOGIA_OPENSOURCE/laravel-logs-layer.git"
    }
]
```

2. Instale a biblioteca usando o Composer:

```bash
composer require ae3/laravel-logs-layer
```

### Channels Disponíveis e Configuração

Esta biblioteca suporta vários canais de captura de logs. Cada canal requer configuração específica por meio de variáveis de ambiente.


**Logstash**

Redirecione logs para a classe LogstashLogger. Configuração em `config/logging.php`:

```php
'channels' => [
    // Outros canais,
    'logstash' => [
        'driver' => 'custom',
        'via' => \Ae3\LaravelLogsLayer\app\Loggers\LogstashLogger::class,
        'host' => env('LOGSTASH_HOST', 'host.docker.internal'),
        'port' => env('LOGSTASH_PORT', 5000),
        'environments' => env('LOGSTASH_ENVIRONMENTS', 'production'),
        'bubble' => true,
        'level' => Logger::DEBUG
    ],
],
```

Variáveis de Ambiente necessárias:

```env
LOGSTASH_HOST=seu_host_logstash
LOGSTASH_PORT=porta_logstash
LOGSTASH_ENVIRONMENTS=ambientes_ativacao
```
---
**Discord**

Redirecione logs para a classe DiscordLogger. Configuração em `config/logging.php`:

```php
'channels' => [
    // Outros canais,
    'discord' => [
        'driver' => 'custom',
        'via' => \Ae3\LaravelLogsLayer\app\Loggers\DiscordLogger::class,
        'bubble' => false,
        'webhook' => env('DISCORD_LOG_CHANNEL_WEBHOOK'),
        'environments' => env('DISCORD_LOG_CHANNEL_ENVIRONMENTS', 'production'),
        'bubble' => true,
        'level' => Logger::ERROR
    ],
],
```

Variáveis de Ambiente necessárias:

```env
DISCORD_LOG_CHANNEL_WEBHOOK=webhook_discord
DISCORD_LOG_CHANNEL_ENVIRONMENTS=ambientes_ativacao
```
---
**E-mail**

Redirecione logs para a classe EmailLogger. Configuração em `config/logging.php`:

```php
'channels' => [
    // Outros canais,
    'email' => [
        'driver' => 'custom',
        'via' => \Ae3\LaravelLogsLayer\app\Loggers\EmailLogger::class,
        'subject' => env('APP_NAME') . ' - Error Log',
        'host' => env('MAIL_HOST'),
        'port' => env('MAIL_PORT'),
        'from' => env('MAIL_FROM'),
        'to' => env('EMAIL_LOG_CHANNEL_TO'),
        'email' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'encryption' => env('MAIL_ENCRYPTION'),
        'debug' => env('MAIL_DEBUG', false),
        'environments' => env('EMAIL_LOG_CHANNEL_ENVIRONMENTS', 'production'),
        'bubble' => true,
        'level' => Logger::CRITICAL,
    ],
],
```

Variáveis de Ambiente necessárias:

```env
APP_NAME=nome_aplicacao
MAIL_HOST=host_email
MAIL_PORT=porta_email
MAIL_FROM=remetente_email
MAIL_USERNAME=usuario_email
MAIL_PASSWORD=senha_email
MAIL_ENCRYPTION=criptografia_email
MAIL_DEBUG=debug_email (opcional)
EMAIL_LOG_CHANNEL_TO=destinatario_email
EMAIL_LOG_CHANNEL_ENVIRONMENTS=ambientes_ativacao
```
---

É possível habilitar mais de um canal para envio de logs. Para isso, basta adicionar à entrada channels de stack, os canais desejados em `config/logging.php`:

```php
'stack' => [
    'driver' => 'stack',
    'channels' => ['email', 'discord', 'logstash'],
    'ignore_exceptions' => false,
],  
```
No exemplo acima, os logs serão enviados para os canais de e-mail, Discord e Logstash.


### Uso

Para disparar um log de erro, utilize a trait [LogTrait](src/app/Traits/LogTrait.php) em suas classes. Certifique-se de configurar as variáveis de ambiente relevantes para o canal escolhido.

Exemplo:

```php

use Ae3\LaravelLogsLayer\app\Traits\LogTrait;

class MyClass
{
    use LogTrait;

    public function myMethod()
    {
        try {
            // Código que pode gerar um erro
        } catch (Exception $e) {
            $loggedExceptionDTO = $this->logException(__METHOD__, $e);
        }
    }
}
```

No caso do método [logException](src/app/Traits/LogTrait.php), o primeiro parâmetro é o nome do método que está chamando o log, o segundo parâmetro é a exceção que foi capturada e o terceiro parâmetro é o nível do log (opcional, padrão: `error`).
Existem três níveis de log: `error`, `critical` e `emergency`.

O retorno do método [logException](src/app/Traits/LogTrait.php) é um objeto do tipo **LoggedExceptionDTO**. Esse objeto contém informações sobre o log que foi capturado, como o código do log, o nível do log, o nome do método que chamou o log, a mensagem de erro, o stack trace, entre outros.

Você pode utilizar essas informações para exibir uma mensagem de erro amigável para o usuário ou para registrar o log em um banco de dados, por exemplo.

#### Como saber qual nível de log de erro utilizar?

1) **Emergency:** O nível de log **emergency** é o mais alto em termos de gravidade. Ele deve ser usado para situações em que ocorrem erros graves que requerem intervenção imediata, uma vez que podem representar uma interrupção crítica ou completa do funcionamento da aplicação. Exemplos incluem falhas graves de infraestrutura, perda de conexão com bancos de dados essenciais, falhas de segurança críticas, entre outros.
2) **Critical:** O nível **critical** é usado para erros críticos que também requerem atenção urgente, embora possam não ser tão catastróficos quanto os eventos de emergência. Esses erros têm um impacto significativo na operação da aplicação e exigem uma investigação e correção imediatas. Um exemplo de uso seria quando uma funcionalidade vital da aplicação falha, mas a aplicação ainda consegue continuar operando em um modo limitado.
3) **Error:** O nível **error** é um nível de gravidade menor em comparação com os anteriores. Ele é usado para registrar erros que ocorrem na aplicação, mas não são tão críticos a ponto de interromper completamente o funcionamento da aplicação. Erros desse tipo não impedem a aplicação de continuar operando, mas ainda assim precisam ser corrigidos para evitar problemas futuros ou impactos negativos nos usuários. Exemplos podem incluir erros de validação de entrada, erros de banco de dados não críticos, falhas de autenticação, entre outros.


### Outros tipos de log fornecidos por `LogTrait`

A trait `LogTrait` fornece uma série de outros métodos para simplificar a captura de logs que não sejam erros.

- `logInfo(string $message, array $customData = [])`: Captura um log de informação.
- `logNotice(string $message, array $customData = [])`: Captura um log de aviso.
- `logWarning(string $message, array $customData = [])`: Captura um log de advertência.
- `logDebug(string $message, array $customData = [])`: Captura um log de depuração.
- `logAlert(string $message, array $customData = [])`: Captura um log de alerta.

### Dica importante

Algumas informações são capturadas automaticamente pela biblioteca e incluídas no log. Essas informações são:

- Usuário logado (se houver).
- URL atual (se houver).
- SQL executado (se houver).
- Guzzle request (se houver) [^1].

Você também pode incluir informações que você julgar importantes para a análise do log. Para isso, utilize o parâmetro `$customData` dos métodos de log. Esse parâmetro deve ser um array associativo, onde a chave é o nome do campo e o valor é o valor do campo. Exemplo:

```php
$this->logException(__METHOD__, $e, 'error', [
    'info1' => $info1,
    'info2' => $info2
]);
```
___

[^1]: Para capturar o Guzzle request, é necessário utilizar o middleware [GuzzleLoggingMiddleware](src/app/Middlewares/GuzzleLoggingMiddleware.php) fornecido pela biblioteca. Segue abaixo um exemplo:

```php
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Client;
use Ae3\LaravelLogsLayer\app\Middlewares\GuzzleLoggingMiddleware;

$handlerStack = HandlerStack::create(new CurlHandler());
$handlerStack->push(new GuzzleLoggingMiddleware(), 'logger');

$clientOptions['handler'] = $handlerStack;
$client = new Client($clientOptions);
```

**Contribuições e Licença**

Contribuições para esta biblioteca são bem-vindas! Sinta-se à vontade para contribuir com melhorias, correções de bugs ou novos recursos por meio de pull requests no repositório oficial: [https://git.ae3tecnologia.com.br/AE3_TECNOLOGIA_OPENSOURCE/laravel-logs-layer](https://github.com/ae3/laravel-logs-layer)

Esta biblioteca é licenciada sob [LICENCE](LICENSE).