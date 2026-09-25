# Arquitetura

```mermaid
flowchart LR
    subgraph Web[ ]
      direction LR
    s1[Servidor A]
    s2[Servidor B]
    end      
    usr(Usuário) e1@--> lb(LoadBalance)
    lb e2@-->Web
    Web e3@-->G[(DB Postgre)]
    Web e4@-->D@{ shape:  h-cyl  , label: "Nas Storage" }

    e1@{ animate: true }
    e2@{ animate: true }
    e3@{ animate: true }
    e4@{ animate: true }
```

## Loadbalance

Faz o balanceamento de carga entre os servidores A e B conforme disponibilidade dos servidores. Certificado SSL (HTTPS) são configurados nessa camada.

## Servidores A e B

Camada Web da aplicação: Composta por 2 servidores gêmeos que hospedam tanto o backend quanto o frontend, que se comunicam com endereços distintos e API JSON.

```
Apache 2
RedHat 9
```

## Backend

```
PHP 8.3
Laravel 11
```

## Frontend

```
Vue.js 3
Vuetify 3
```