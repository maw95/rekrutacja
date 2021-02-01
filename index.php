<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
        <style>
            button, div
            {
                border: 2px solid #333333;
                background-color: #333333;
                color: #fff;
                border-radius: 0.5em;
                font-size: 2em;
                text-align: center;
                padding: 1em 2em;
                transition: 0.3s all ease-in-out;
                cursor: pointer;
            }
            button:hover
            {
                color: #333333;
                background-color: #fff;
            }
        </style>
    </head>
    <body>
        <a href="<?php echo $_SERVER['REQUEST_URI'] . 'zadanie1.php'?>">
            <button>
                Zadanie 1
            </button>
        </a>
        <a href="<?php echo $_SERVER['REQUEST_URI'] . 'zadanie2.php'?>">
            <button>
                Zadanie 2
            </button>
        </a>
    </body>
</html>