<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Actualités</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="./style.css"/>
    </head>
    <body>
        <header>
            <a href='admin.php'><img src="logo_dis_serena.png" alt="Logo Dis Serena"/></a>
            <?php include("menu.php"); ?>
        </header>
        <div id="wrapper">
            <aside>
                <?php include("userimg.php"); ?>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les derniers messages de
                        tous les utilisatrices du site.</p>
                </section>
            </aside>
            <main>

                <?php
       

                // Etape 1: Ouvrir une connexion avec la base de donnée.
                //FAIT
                include("connect.php");
                //verification
                if ($mysqli->connect_errno)
                {
                    echo "<article>";
                    echo("Échec de la connexion : " . $mysqli->connect_error);
                    echo("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
                    echo "</article>";
                    exit();
                }

                // Etape 2: Poser une question à la base de donnée et récupérer ses informations
                // cette requete vous est donnée, elle est complexe mais correcte, 
                // si vous ne la comprenez pas c'est normal, passez, on y reviendra
                //FAIT
                $laQuestionEnSql = "
                    SELECT posts.content,posts.created,users.alias AS author_name, users.id AS UserID,  
                    COUNT(likes.id) AS like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist,
                    GROUP_CONCAT(DISTINCT tags.id ORDER BY tags.label) AS taglistid -- ajout id tag
                    FROM posts
                    JOIN users ON users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes ON likes.post_id  = posts.id 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    LIMIT 5
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                // Vérification
                if ( ! $lesInformations)
                {
                    echo "<article>";
                    echo("Échec de la requete : " . $mysqli->error);
                    echo("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
                    echo "</article>";
                    exit();
                }

                // Etape 3: Parcourir ces données et les ranger bien comme il faut dans du html
                // NB: à chaque tour du while, la variable post ci dessous reçois les informations du post suivant.
                //FAIT
                while ($post = $lesInformations->fetch_assoc())
                {
                    //la ligne ci-dessous doit etre supprimée mais regardez ce 
                    //qu'elle affiche avant pour comprendre comment sont organisées les information dans votre 
                    //echo "<pre>" . print_r($post, 1) . "</pre>";

                    // @todo : Votre mission c'est de remplacer les AREMPLACER par les bonnes valeurs
                    // ci-dessous par les bonnes valeurs cachées dans la variable $post 
                    // on vous met le pied à l'étrier avec created
                    // 
                    // avec le ? > ci-dessous on sort du mode php et on écrit du html comme on veut... mais en restant dans la boucle
                    //FAIT
                    if (!empty($post['taglist'])) {
                    $explode = explode(",", $post['taglist']);
                    }else{
                        $explode = [];
                    };
                    if (!empty($post['taglistid'])) {
                    $explodeid = explode(",", $post['taglistid']);
                    } else {
                        $explodeid = [];
                    }
                    ?>
                    <article>
                        <h3>
                            <time><?php echo $post['created'] ?></time>
                        </h3>
                        <address><a href="wall.php?user_id=<?php echo $post["UserID"]; ?>"><?php echo $post['author_name'] ?></a></address>
                        <div>
                            <p><?php echo $post['content'] ?></p>
                        </div>
                        <footer>
                            <small>♥ <?php echo $post['like_number'] ?></small>
                                    <?php if (count($explodeid) > 0) {
                                    for ($i = 0; $i < count($explodeid); $i++) { ?>
                                    <a href="tags.php?tag_id=<?php echo $explodeid[$i]; ?>">#<?php echo $explode[$i]; ?></a>
                                    <?php } ?>
                                    <?php } else { ?> 
                                        <a href="tags.php?tag_id=<?php echo $MoreTagId; ?>"><em>More Tags [...]</em></a>
                                    <?php } ?>
                        </footer>
                    </article>
                    <?php
                }
                ?>
            </main>
        </div>
    </body>
</html>
