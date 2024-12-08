# desafiofullstackback

> Descrição:
#### API feita com láravel (framework do PHP) seguindo os princípios do REST (Representional State Transfer), trata-se de uma arquitetura de software que define o funcionamento de uma API). Este back-end foi desenvolvido para realização de um desafio full stack para a empresa E-inov. Ela contém rotas para gerenciamento de clientes, telefone e uma para dar início a um job de envio de e-mails. Cada usuário pode ter mais de um número, fazendo uma relação OneToMany (um para muitos, onde um cliente pode ter muitos números. O job envia para todos os usuários cadastrados e-mails contendo notícias diárias obtidas através do uol: https://rss.uol.com.br/feed/tecnologia.xml. Basicamente eu fiz um script que ficará rodando em segundo plano o dia inteiro e que no surgimento de notícias irá enviá-las aos usuários. Para isso tive que utilizar recursos como o redis, que por se tratar de armazenamento de estruturas de dados NoSql (não relacional) trouxe performance a este serviço que utilizando um SGBD (Sistema de Gerenciamento de Banco de dados) Relacional seria muito custoso, já que na lógica um usuário não pode receber a mesma notícia mais de uma vez. Além disso a API está documentada com swagger, vale ressaltar o uso de paginação em retornos de listas de usuários e telefones que reforça ainda mais os princípios do REST utilizados neste projeto e também contém alguns testes unitários.

#### Captura de tela do swagger:
![Captura de tela 2024-12-08 0001481](https://github.com/user-attachments/assets/61eda17a-5ca3-4c7b-9210-5c3e37772a6a)

#### Captura de tela da execução de testes:
![Captura de tela 2024-12-08 000005](https://github.com/user-attachments/assets/20813fc4-676d-4bcf-8b62-ab39c3b1bb96)

#### Captura de tela de um template de e-mail contendo uma notícia:
![Captura de tela 2024-12-08 0003012](https://github.com/user-attachments/assets/fe9c7ea5-4015-43f7-82a5-3703818afae8)

> Execução:
#### 
~~~
php artisan serve
~~~
