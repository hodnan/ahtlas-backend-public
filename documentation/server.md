# Servidor
## Apache (root)
```
systemctl start httpd
systemctl stop httpd
systemctl restart httpd
systemctl status httpd

/usr/local/lib/php.ini
/etc/httpd
/etc/httpd/conf.d
```

## Supervisor (root) Rotinas no servidor A
```
supervisorctl status
supervisorctl reread // para identificar novos arquivos de agendamento
supervisorctl update // para aplicar novos arquivos

systemctl start supervisord
systemctl stop supervisord
systemctl restart supervisord
systemctl status supervisord

Manutenção 
verifique se o serviço agendado está ativo
ps aux | grep 'artisan schedule:work'
ps aux | grep 'php artisan queue:work --queue=ccp-mail'

ser não extiver ativo, delete o arquivo de agendamento e atualize o supervisord, coloque o aquivo novamente e atualize o supervisord 

```

# Banco

## PostgreSql

Lista todos os usuário/regras

```
SELECT * FROM pg_roles;
```

Dar acesso leitura em uma tabela para um usuário
```
GRANT SELECT ON TABLE addons.kpi_results TO "app_reader";
```

