<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Termo</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px ;
        }

        .pre {
            margin: 0;
            font-family: Arial, sans-serif;
            font-size: 11px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .payment-rules {
            counter-reset: section;
            padding-left: 0;
            margin-top: 0;
        }
        .payment-rules > li {
            list-style: none;
            position: relative;
            margin-bottom: 3px;
            padding-left: 25px;
        }
        .payment-rules > li::before {
            counter-increment: section;
            content: counter(section) ".";
            position: absolute;
            left: 0;
            color: gray; /* Adiciona a cor vermelha aos marcadores numéricos */
            font-weight: bold;
        }
       

        /*  */
        .table_header{
            background-color: #ccc0d9;
            font-size: 10px;
            text-transform: uppercase;
        }
       
        .basket{
            background-color: #ccc0d9;
        }
      
        .accelerator{
            background-color: #ccc0d9;
        }
        .deflator{
            background-color: #ccc0d9;
        }
        .knockout{
            background-color: #ccc0d9;
        }

        .uppercase{
            text-transform: uppercase;
        }
        .capitalize{
            text-transform: capitalize;
        }
        .explan{
            font-size: 10px;
            padding-left: 20px;
            padding-right: 20px;
        }
    </style>
</head>

<body>
    @section('body')
    @show
</body>
</html>