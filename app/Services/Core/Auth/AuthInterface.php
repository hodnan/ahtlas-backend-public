<?php

namespace App\Services\Core\Auth;

interface AuthInterface
{
    // Endpoints dos provedores externos ficam em config/services.php
    // (services.corporate_idp e services.ldap_directory).
    // Rótulos de ambiente por hostname ficam em config/ahtlas.php (ahtlas.environments).

    const TYPES = [
         'ext' => [ 'label' => 'Externo', 'provider' => 'ldap'],
         'usr' => [ 'label' => 'Corporativo', 'provider' => 'corporate'],
    ];


}
