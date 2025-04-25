# 📦 Kanban de Tarefas - Backend (Laravel)

Este é o backend do projeto **Kanban de Tarefas**, desenvolvido com Laravel. Ele expõe uma API RESTful protegida por autenticação via Laravel Sanctum, utilizada pelo frontend Vue 3.

## 🚀 Tecnologias

- Laravel 12  
- Sanctum (Autenticação)  
- MySQL  
- PHPUnit (TDD - Test Driven Development)  
- Swagger (Documentação da API)

## 📚 Funcionalidades

- Autenticação com Sanctum  
- CRUD de Tarefas  
- Limite de até 10 tarefas por dia por usuário  
- Filtro automático por data (somente tarefas do dia atual)  
- Bloqueio de tarefas concluídas (sem retorno ao fluxo anterior)

## 🧪 Testes

Os testes são escritos com **PHPUnit**, seguindo a metodologia TDD:

```bash
php artisan test
```

## 🛠 Instalação Local

```bash
git clone https://github.com/seu-usuario/kanban-tarefas-backend.git
cd kanban-tarefas-backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

## 🔐 Autenticação

A autenticação é baseada em tokens com Laravel Sanctum.  
Após login, o frontend deve armazenar e enviar o token nos headers:

```
Authorization: Bearer {token}
```

## 📄 Documentação da API

A documentação da API foi gerada com Swagger.  
Acesse em: 

https://task-manager-0wdh.onrender.com/api/documentation/documentation


## 🌐 Deploy

O backend está publicado gratuitamente na plataforma **Render**.

## 🔗 Links Úteis

- 🔗 API Live: [https://task-manager-web-pxgc.onrender.com/](https://task-manager-web-pxgc.onrender.com/)  
- 📘 Documentação Swagger: `https://task-manager-0wdh.onrender.com/api/documentation`