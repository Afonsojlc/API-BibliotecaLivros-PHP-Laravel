# 📚 Biblioteca Digital API | Laravel REST Engine

> **Projeto Académico:** Unidade Curricular de Backend (Programação Web)  
> **Tecnologias:** Laravel 11, PHP 8.2+, SQLite, Sanctum.

---

## 📋 Sobre o Projeto

Esta API RESTful foi desenvolvida como projeto final para a cadeira de **Programação Web (Backend)**. O objetivo foi transpor os conceitos de arquitetura de microserviços e APIs (previamente explorados em Node.js) para o ecossistema **PHP/Laravel**, focando em produtividade, segurança e escalabilidade.

O sistema gere o catálogo e o fluxo de reservas de uma biblioteca, implementando um modelo de dados relacional complexo e um sistema de autenticação robusto.

### 🛠️ Diferenciais Técnicos
* **Arquitetura MVC:** Separação clara de responsabilidades para facilitar a manutenção.
* **Segurança (Sanctum):** Autenticação *stateless* via tokensBearer.
* **RBAC (Role-Based Access Control):** Diferenciação de permissões entre `Admin` (Gestão de acervo) e `Leitor` (Consumo e reservas).
* **Data Integrity:** Validação rigorosa de inputs e relações entre tabelas (Eloquent ORM).

---

## 🏗️ Estrutura de Dados & Relacionamentos

A API baseia-se num modelo relacional otimizado:
* **Autores & Livros:** Relação `1:N` (Um autor possui múltiplos livros).
* **Utilizadores & Livros:** Relação `N:M` (Pivot: **Reservas**), permitindo o rastreio histórico de requisições.

---

## 🚀 Guia de Instalação (Local Setup)

Segue estes passos para colocar o ambiente a funcionar na tua máquina:

### 1. Clonar e Instalar Dependências
```bash
git clone [https://github.com/teu-utilizador/teu-repositorio.git](https://github.com/teu-utilizador/teu-repositorio.git)
cd teu-repositorio
composer install