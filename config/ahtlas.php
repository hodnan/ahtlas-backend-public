<?php

/*
|--------------------------------------------------------------------------
| Ahtlas
|--------------------------------------------------------------------------
|
| Parâmetros específicos da aplicação. Listas de matrículas são lidas do
| .env como valores separados por vírgula.
|
*/

$list = fn (string $key): array => array_values(array_filter(array_map('trim', explode(',', (string) env($key, '')))));

return [

    // Matrícula do CEO (notificações de nível máximo)
    'ceo' => env('AHTLAS_CEO'),

    // Matrículas que recebem cópia oculta das notificações administrativas
    'admins' => $list('AHTLAS_ADMINS'),

    // Domínio usado para montar o e-mail corporativo a partir da matrícula
    'users' => [
        'email_domain' => env('AHTLAS_USERS_EMAIL_DOMAIN', 'example.com'),
    ],

    // Hostname do servidor => rótulo exibido para o ambiente
    'environments' => [
        'localhost' => 'Desenvolvimento',
        'app-hml' => 'Homologação',
        'app-prd-01a' => 'Prod - 01A',
        'app-prd-01b' => 'Prod - 01B',
    ],

    'control_center' => [
        // Equipe CDC em cópia nos e-mails de acompanhamento
        'cdc_users' => $list('AHTLAS_CONTROL_CENTER_CDC_USERS'),
    ],

    'reports' => [
        // Gestores N3 cujas equipes (staff) podem ser donas de relatórios
        'owner_managers_n3' => $list('AHTLAS_REPORTS_OWNER_MANAGERS_N3'),
        // Usuários avulsos que também podem ser donos de relatórios
        'owner_users' => $list('AHTLAS_REPORTS_OWNER_USERS'),
        // Usuários que recebem a lista de erros de diagnóstico na leitura
        'debug_users' => $list('AHTLAS_REPORTS_DEBUG_USERS'),
    ],

    'kpi_sources' => [
        // Gestor N3 cuja equipe pode ser dona de fontes de KPI
        'owner_manager_n3' => env('AHTLAS_KPI_SOURCES_OWNER_MANAGER_N3'),
    ],

    'ai_power' => [
        // Destinatário dos e-mails do Potênc-IA
        'mail_to' => env('AHTLAS_AI_POWER_MAIL_TO'),
        'mail_to_name' => env('AHTLAS_AI_POWER_MAIL_TO_NAME'),
    ],

];
