<?php session_start();
include("./connect.php");
$SqlRequest = "
SELECT tag_id, COUNT(*) as occurrence_count
FROM posts_tags
GROUP BY tag_id
ORDER BY occurrence_count DESC
LIMIT 1
";
$Infos = $mysqli->query($SqlRequest);
$TagsId = $Infos->fetch_assoc();
$MoreTagId = $TagsId['tag_id'];
?>
<nav id="menu">
    <a href="news.php">Actualités</a>

    <?php if (isset($_SESSION['connected_id']) && $_SESSION['connected_id'] !== null) : ?>
        <a href="wall.php?user_id=<?php echo $_SESSION['connected_id']; ?>">Mur</a>
    <?php else: ?>
        <a href="#" onclick="alert('Veuillez vous connecter pour avoir accès au mur');">Mur</a>
    <?php endif; ?>

    <?php if (isset($_SESSION['connected_id']) && $_SESSION['connected_id'] !== null) : ?>
        <a href="feed.php?user_id=<?php echo $_SESSION['connected_id']; ?>">Flux</a>
    <?php else: ?>
        <a href="#" onclick="alert('Veuillez vous connecter pour avoir accès au flux');">Flux</a>
    <?php endif; ?>
    
    <a href="tags.php?tag_id=<?php echo $MoreTagId; ?>">Tags</a>
</nav>
<nav id="user">
    <?php if( isset($_SESSION['connected_id']) && $_SESSION['connected_id'] !== null ) : ?>
        <?php else: ?>
                <a href="./login.php">Connexion</a>
        <?php endif; ?> 
</nav>
<?php if (isset($_SESSION['connected_id']) && $_SESSION['connected_id'] !== null) : ?>
    <nav id="user">
        <img src="./user01.png" alt="profil icon" />
        <a href="#"><?php echo $_SESSION['connected_alias']; ?></a>
    <ul>
            <li><a href="settings.php?user_id=<?php echo $_SESSION['connected_id']; ?>">Mes Paramètres</a></li>
            <li><a href="followers.php?user_id=<?php echo $_SESSION['connected_id']; ?>">Mes Followers</a></li>
            <li><a href="subscriptions.php?user_id=<?php echo $_SESSION['connected_id']; ?>">Mes Abonnements</a></li>
            <li><a href="./logout.php">Déconnexion</a></li>
        </ul>
</nav>
<?php endif; ?>