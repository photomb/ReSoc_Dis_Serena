<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mes abonnés </title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
            <img src="./logo_dis_serena.png" alt="Logo de notre réseau social"/> 
            <?php include("menu.php"); ?>
        </header>
        <div id="wrapper">          
            <aside>
                <?php include("userimg.php"); ?>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez la liste des personnes qui
                        suivent les messages de l'utilisatrice
                        n° <?php echo intval($_GET['user_id']) ?></p>

                </section>
            </aside>
            <main class='contacts'>
                <?php
                // Etape 1: récupérer l'id de l'utilisateur
                //FAIT
                $userId = intval($_GET['user_id']);
                // Etape 2: se connecter à la base de donnée
                //FAIT
                include("connect.php");
                // Etape 3: récupérer le nom de l'utilisateur
                //FAIT
                $laQuestionEnSql = "
                    SELECT users.*
                    FROM followers
                    LEFT JOIN users ON users.id=followers.following_user_id
                    WHERE followers.followed_user_id='$userId'
                    GROUP BY users.id
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                while($foll= $lesInformations->fetch_assoc()){
                // Etape 4: à vous de jouer
                //@todo: faire la boucle while de parcours des abonnés et mettre les bonnes valeurs ci dessous 
                //FAIT
                ?>
                <article>
                    <img src="user.jpg" alt="blason"/>
                    <h3><a href="wall.php?user_id=<?php echo $foll["id"]; ?>"><?php echo $foll["alias"]; ?></a></h3>
                    <p>id:<?php echo $foll["id"]; ?></p>
                </article>
                <?php } ?>
            </main>
        </div>
    </body>
</html>
