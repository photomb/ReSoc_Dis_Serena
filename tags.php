<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Les message par mot-clé</title>
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
             * Cette page est similaire à wall.php ou feed.php
             * mais elle porte sur les mots-clés (tags)
             */
            /**
             * Etape 1: Le mur concerne un mot-clé en particulier
             * FAIT
             */
            $tagId = intval($_GET['tag_id']);
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
                 * Etape 3: récupérer le nom du mot-clé
                 * FAIT
                 */
                $laQuestionEnSql = "SELECT * FROM tags WHERE id= '$tagId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $tag = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par le label et effacer la ligne ci-dessous
                //echo "<pre>" . print_r($tag, 1) . "</pre>";
                //FAIT
                include("userimg.php");
                ?>
                <section>
                    <h3>Présentation</h3>
                    <p>Vous êtes sur la page des Tags. Actuellement sont affichés les derniers messages comportant
                        le tag <strong>#<?php echo $tag['label']; ?></strong>
                        (n°<?php echo $tag['id']?>)
                    </p>
                    <?php
                        $enCoursDeTraitement = isset($_POST['searchtags']);
                        
                        if ($enCoursDeTraitement) {
                        $searchTag = $_POST['searchtags'];
                        $searchTag = $mysqli->real_escape_string($searchTag);
                    }
                    ?>
                    <form action="redirectags.php" method="post">
                        <label for="searchtags">Tags Search : </label>
                        <select name="searchtags" id="searchtags"/>
                                <option value="">--Sélectionner un tag</option>
                                    <?php
                                        //requete rapide
                                        $listTags = [];
                                        $laQuestionEnSql2 = "SELECT * FROM tags";
                                        $lesInformations2 = $mysqli->query($laQuestionEnSql2);
                                        //boucle recup tags dans bdd
                                        while ($tag = $lesInformations2->fetch_assoc())
                                        {
                                            $listTags[$tag['id']] = $tag['label'];
                                        }  
                                        //gen menu deroulant tags
                                        foreach ($listTags as $id => $label)
                                        echo "<option value='$id'>#$label</option>";
                                    ?> 
                        </select>
                        <input type="submit" value="Search"/>
                        <?php 
                        ?>
                    </form>
                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 3: récupérer tous les messages avec un mot clé donné
                 * FAIT
                 */
                $laQuestionEnSql = "
                    SELECT posts.content,posts.created,users.alias AS author_name, users.id AS UserID,
                    COUNT(likes.id) AS like_number,
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist,
                    GROUP_CONCAT(DISTINCT tags.id ORDER BY tags.label) AS taglistid
                    FROM posts_tags AS filter
                    JOIN posts ON posts.id=filter.post_id
                    JOIN users ON users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id
                    LEFT JOIN likes      ON likes.post_id  = posts.id
                    WHERE filter.tag_id = '$tagId'
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
                 * FAIT
                 */
                while ($post = $lesInformations->fetch_assoc())
                {
                    //echo "<pre>" . print_r($post, 1) . "</pre>";
                    //echo "<pre>" . print_r(explode(",", $post['taglistid'])) . "</pre>";
                    $explode = explode(",", $post['taglist']);
                    $explodeid = explode(",", $post['taglistid']);
                ?>
                    <article>
                        <h3>
                            <time><?php echo $post["created"]; ?></time>
                        </h3>
                        <address>par <a href="wall.php?user_id=<?php echo $post["UserID"]; ?>"><?php echo $post["author_name"]; ?></a></address>
                        <div>
                            <p><?php echo $post["content"]; ?></p>
                        </div>
                        <footer>
                            <small>♥ <?php echo $post["like_number"]; ?></small>
                            <?php if (count($explodeid) > 0): 
                                for ($i = 0; $i < count($explodeid); $i++) { ?>
                                <a href="tags.php?tag_id=<?php echo $explodeid[$i]; ?>">#<?php echo $explode[$i]; ?></a>
                                <?php ;} ?>
                            <?php else: ?>
                                <a href="#">More Tags [...]</a>
                            <?php endif ?>
                        </footer>
                    </article>
                <?php } ?>
            </main>
        </div>
    </body>
</html>