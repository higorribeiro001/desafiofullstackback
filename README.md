# desafiofullstackback

> Descrição:
#### API feita com láravel (framework do PHP) seguindo os princípios do REST (Representional State Transfer), trata-se de uma arquitetura de software que define o funcionamento de uma API). Este back-end foi desenvolvido para realização de um desafio full stack para a empresa E-inov. Ela contém rotas para gerenciamento de clientes, telefone e uma para dar início a um job de envio de e-mails. Cada usuário pode ter mais de um número, fazendo uma relação OneToMany (um para muitos), onde um cliente pode ter muitos números e ao ser cadastrado será executado um job para enviar e-mail com a confirmação do cadastro. Outro job envia para todos os usuários cadastrados e-mails contendo notícias diárias obtidas através do uol: https://rss.uol.com.br/feed/tecnologia.xml. Basicamente eu fiz um script que ficará rodando em segundo plano o dia inteiro e que no surgimento de notícias irá enviá-las aos usuários, por este motivo, fiz um endpoint contendo somente um método que irá dar início ao job, por achar muito arriscada de colocar em um dos já existentes que serião usados constantemente e abriria brecha para um gargalo ou redução de performance. Para evitar isso tive que utilizar recursos como o redis, que por se tratar de armazenamento de estruturas de dados NoSql (não relacional) trouxe boa performance a este serviço que utilizando um SGBD (Sistema de Gerenciamento de Banco de dados) Relacional seria muito custoso, já que na lógica um usuário não pode receber a mesma notícia mais de uma vez. Além disso a API está documentada com swagger, vale ressaltar o uso de paginação em retornos de listas de usuários e telefones que reforça ainda mais os princípios do REST utilizados neste projeto e também contém alguns testes unitários para os métodos de usuário e telefone.

#### Captura de tela do swagger:
![Captura de tela 2024-12-08 0001481](https://github.com/user-attachments/assets/61eda17a-5ca3-4c7b-9210-5c3e37772a6a)

#### Captura de tela da execução de testes:
![Captura de tela 2024-12-08 000005](https://github.com/user-attachments/assets/20813fc4-676d-4bcf-8b62-ab39c3b1bb96)

#### Captura de tela de um template de e-mail contendo uma notícia:
![Captura de tela 2024-12-08 0003012](https://github.com/user-attachments/assets/fe9c7ea5-4015-43f7-82a5-3703818afae8)

> Execução:
#### Após clonar o projeto, deve-se instalar todas as dependencias:
~~~~
composer install
~~~~
#### Em sequência configurar as variáveis de ambiente, crie um arquivo .env e cole o seguinte conteúdo:
~~~~
APP_NAME=Desafio
APP_ENV=local
APP_KEY=base64:U3PZgaPqFlhvlGE4C954y79WSPMtFZKkaz/cPCmLUq8=
APP_DEBUG=true
APP_URL=https://einov.com/
APP_INOV=https://einov.com/

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_CLIENT=predis

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=sistemaamz@gmail.com
MAIL_PASSWORD="yola hqtm harf jluc"
MAIL_ENCRYPTION=TLS
MAIL_FROM_ADDRESS="${MAIL_USERNAME}"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

SESSION_DOMAIN=localhost
SANCTUM_STATEFUL_DOMAINS=localhost
L5_SWAGGER_CONST_HOST=http://project.test/api/v1
~~~~
#### A partir daqui será essencial que tenha o Docker instalado em sua máquina para rodar o redis, mas poderá utilizar outra maneira se preferir (só não esqueça de configurar o redis no .env). Com docker basta executar os seguintes comandos:
~~~~
docker pull redis:6.0.20-bookworm
~~~~
#### Deve-se criar um volume:
~~~~
docker volume create redisdb
~~~~
#### Por fim basta executar:
~~~~
docker run -v redisdb>/data -p 6379:6379 --name redisdb redis:6.0.20-bookworm
~~~~
#### Caso queira rodar os testes unitários, para os testes de usuários:
~~~~
php artisan test --filter=UserControllerTest 
~~~~
#### E os de telefones:
~~~~
php artisan test --filter=PhoneControllerTest 
~~~~
#### Antes de iniciar o job de e-mails pelo endpoint, deve executar o seguinte comando em outro terminal:
~~~~
php artisan queue:work --tries=3
~~~~
#### Obs: tries é para que em caso de falha ele tente mais duas vezes.

#### Por último, é só executar o comando abaixo e a API estará no ar:
~~~
php artisan serve
~~~

#### Para acessar o Swagger: http://localhost:8000/api/documentation#/
