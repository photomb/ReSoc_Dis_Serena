<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Flux</title>         
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
            <img src="./logo_dis_serena.png" alt="Logo de notre réseau social"/>
        <?php include("menu.php"); ?>
        </header>
        <div id="wrapper">
            <?php
            /**
             * Cette page est TRES similaire à wall.php. 
             * Vous avez sensiblement à y faire la meme chose.
             * Il y a un seul point qui change c'est la requete sql.
             */
            /**
             * Etape 1: Le mur concerne un utilisateur en particulier
             * FAIT
             */
            $userId = intval($_GET['user_id']);
            ?>
            <?php
            /**
             * Etape 2: se connecter à la base de donnée
             * FAIT
             */
            include("connect.php");
            ?>

            <aside>
                <?php
                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 * FAIT
                 */
                $laQuestionEnSql = "SELECT * FROM `users` WHERE id= '$userId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
                //echo "<pre>" . print_r($user, 1) . "</pre>";
                //FAIT
                include("userimg.php");
                ?>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les message des utilisatrices
                        auxquel est abonnée l'utilisatrice <?php echo $user['alias']; ?>
                        (n° <?php echo $userId ?>)
                    </p>

                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 3: récupérer tous les messages des abonnements
                 * FAIT
                 */
                $laQuestionEnSql = "
                    SELECT posts.content,posts.created,users.alias AS author_name, users.id AS UserID, 
                    COUNT(likes.id) AS like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist,
                    GROUP_CONCAT(DISTINCT tags.id ORDER BY tags.label) AS taglistid -- ajout id tag
                    FROM followers 
                    JOIN users ON users.id=followers.followed_user_id
                    JOIN posts ON posts.user_id=users.id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE followers.following_user_id='$userId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }

                /**
                 * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
                 * A vous de retrouver comment faire la boucle while de parcours...
                 * FAIT
                 */
                while ($post = $lesInformations->fetch_assoc())
                {
                $explode = explode(",", $post['taglist']);
                $explodeid = explode(",", $post['taglistid']);
                ?>
                <article>
                    <h3>
                        <time><?php echo $post['created']; ?></time>
                    </h3>
                    <address>par <a href="wall.php?user_id=<?php echo $post["UserID"]; ?>"><?php echo $post['author_name']; ?></a></address>
                    <div>
                        <p><?php echo $post['content']; ?></p>
                    </div>                                            
                    <footer>
                        <small>♥ <?php echo $post['like_number']; ?></small>
                        <?php if (!empty($post['taglist']) && !empty($post['taglistid'])) { ?>
                            <?php if (count($explodeid) > 1):
                                for ($i = 0; $i < count($explodeid); $i++) { ?>
                                <a href="tags.php?tag_id=<?php echo $explodeid[$i]; ?>">#<?php echo $explode[$i]; ?></a>
                            <?php ;} ?>
                            <?php else: ?>
                                <a href="tags.php?tag_id=<?php echo $explodeid[0]; ?>">#<?php echo $explode[0]; ?></a>
                            <?php endif ?>
                        <?php } else { ?>
                            <p></p>
                        <?php } ?>
                    </footer>
                </article>
                <?php } ?>


            </main>
        </div>
    </body>
</html>
