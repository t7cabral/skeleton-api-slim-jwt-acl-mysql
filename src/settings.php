<?php

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'determineRouteBeforeAppMiddleware' => true, //Para o Slim iniciar as Rotas antes do Middleware
        
        // database connection details         
        "db" => [            
            "host" => "localhost",             
            "dbname" => "db_api_slim_modelo",             
            "user" => "root",            
            "pass" => ""        
        ],

        "jwt" => [
            'secret' => base64_encode('iVBORw0KGgoAAAANSUhEUgAAAZAAAAGQCAIAAAAP3aGbAACAAElEQ')
        ]

    ],
];
