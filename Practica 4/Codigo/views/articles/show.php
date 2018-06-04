<div class="main">
    <?php
    if ($this->alertMsg != "") { ?>
        <div class="alert-box">
            <p><i class="fas fa-info-circle"></i> <?php echo($this->alertMsg); ?></p>
        </div>
        <?php
        $this->alertMsg = "";
    } ?>

    <?php if (isset($_SESSION['user_type']) && (($_SESSION['user_type'] == 'gestor') || ($_SESSION['user_type'] == 'superusuario'))) { ?>
        <div class="article-control-pad">
            <strong>Gestión de artículos</strong>
            <p>Puede añadir artículos en la página principal</p>
            <a class="edit button" onclick="displayEditArticleForm();"><i class="far fa-edit" style="margin-right: 5px;"></i> Editar artículo</a>
            <a class="delete button" href="?option=index&action=delete-article&id=<?php echo $article->id ?>"><i class="far fa-trash-alt" style="margin-right: 5px;"></i> Eliminar artículo</a>

            <form class="edit-article-form" id="edit-article" action="?option=show&item=<?php echo $article->id ?>&action=edit-article" method="post" style="display: none;">
                <h2>Editar artículo</h2>
                <label for="title">Título</label>
                <input type="text" placeholder="Título del artículo" name="title" value="<?php echo $article->titulo ?>" required>
                <label for="subtitle">Subtítulo</label>
                <input type="text" placeholder="Subtítulo del artículo" name="subtitle" value="<?php echo $article->subtitulo ?>" required>
                <label for="content">Contenido</label>
                <textarea placeholder="Contenido del artículo" name="content" required><?php echo $article->contenido ?></textarea>
                <label for="main-image">Imagen principal</label>
                <input type="text" placeholder="Localizada en views/img" name="main-image" value="<?php echo $article->imagen_principal ?>" required>
                <label for="footer-image">Pie de la imagen principal</label>
                <input type="text" placeholder="Pie de imagen" name="footer-image" value="<?php echo $article->pie_imagen ?>" required>
                <label for="article-images">Imégenes adicionales del artículo</label>
                <input type="text" placeholder="Localizadas en views/img/galleries" name="article-images" value="<?php
                for($i = 0; $i < count($article->imagenes); $i++) {
                    echo ($article->imagenes[$i] . " ");
                }
                ?>" required>

                <button id="loading-button" class="edit" onclick="swapByLoadingIcon()" type="submit">Actualizar artículo</button>
            </form>
        </div>
    <?php } ?>

    <div class="article">
        <h1><?php echo $article->titulo; ?></h1>
        <p class="date"><?php echo($article->day . "/" . $article->month . "/" . $article->year); ?></p>

        <div class="img-container">
            <a href="views/img/<?php echo $article->imagenes[0]; ?>"><img src="views/img/<?php echo $article->imagenes[0]; ?>" alt=""></a>
            <p><?php echo $article->pie_imagen ?></p>
        </div>

        <p class="description">
            <?php echo $article->contenido; ?>
        </p>

        <?php if (count($article->imagenes) > 1) { ?>
            <div class="photo-gallery">
                <?php
                $i = 0;
                do { ?>
                    <div>
                        <ul class="box2">
                            <?php for ($j = $i; $j < $i + 3; $j++) { ?>
                                <li><img src=views/img/galleries/<?php echo $article->imagenes[$j + 1]?>></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php
                $i = $i + 3;
                } while($i < (count($article->imagenes) - 1))  ?>
            </div>
        <?php } ?>

        <!-- Botones de interaccion -->
        <?php $comment_list = Comment::find($article->id); ?>
        <button class="article-button" type="button" onclick="showComments();"><span id="num-comments"><?php if (count($comment_list) > 0) echo count($comment_list); else echo "Sin" ?></span> comentarios</button>
        <button class="article-button" type="button" onclick="showShare();">Compartir</button>
        <a href="?option=print&item=<?php echo $article->id; ?>"><button class="article-button" type="button">Imprimir</button></a>

        <!-- Bloque de comparticion -->
        <div class="comments-block" id="share-block">
            <h2>Compartir vía</h2>
            <button class="article-button" type="button" style="background-color: #3b5998; color: white; border: 1px solid #3b5998; margin-top: 5px; width: 8em;" onclick="facebookShare();"><i class="fab fa-facebook" style="margin-right: 5px;"></i>  Facebook</button>
            <button class="article-button" type="button" style="background-color: #00aced; color: white; border: 1px solid #00aced; margin-top: 5px; width: 8em;" onclick="twitterShare();"><i class="fab fa-twitter" style="margin-right: 5px;"></i>  Twitter</button>
        </div>

        <!-- Bloque de comentarios -->
        <div class="comments-block" id="comments-block">
            <h2>Comentarios</h2>
            <!-- Informacion sobre el autor -->
            <div class="author-info" id="author-box">
                <img id="author-info-avatar" src="views/img/avatar/david.jpg" alt="avatar del autor">
                <h3 id="author-info-name">David Vargas</h3>
                <button id="author-info-close" class="close" onclick="showInfoBox();">Cerrar</button>
            </div>
            <div id="comment-list">
            <!-- Lista de comentarios -->
            <?php
                $comment_num = 1;
                foreach($comment_list as $comment) { ?>
                    <div class="comment" id="comment-<?php echo $comment_num ?>">
                        <div class="profile-img">
                            <img id="comment-avatar" src=views/img/avatar/<?php echo $comment->imagen; ?>  alt="avatar" onclick="showInfoBox(1, '<?php echo $comment->nombre; ?>', '<?php echo $comment->imagen; ?>');">
                        </div>
                        <div class="comment-text">
                            <p class="comment-author">#<span id="comment-number"><?php echo $comment_num ?></span> <span id="author-name" onclick="showInfoBox(1, '<?php echo $comment->nombre; ?>', '<?php echo $comment->imagen; ?>');"><?php echo $comment->nombre; ?></span><!-- : --></p>
                            <p class="comment-content" id="comment-content"><?php echo $comment->contenido; ?></p>
                            <p class="comment-date">A las <?php echo $comment->formatTime(); ?> el <?php echo $comment->getDate(); ?></p>
                        </div>
                        <!-- Controles de moderacion-->
                        <?php if (isset($_SESSION['user_type']) && (($_SESSION['user_type'] == 'moderador') || ($_SESSION['user_type'] == 'superusuario'))) { ?>
                            <div class="control-pad">
                                <a class="edit button" onclick="displayEditCommentForm(<?php echo $comment->id_comentario ?>);"><i class="far fa-edit" style="margin-right: 5px;"></i> Editar</a>
                                <a class="delete button" href="?option=show&item=<?php echo $_GET['item']; ?>&action=delete&comment_id=<?php echo $comment->id_comentario; ?>"><i class="far fa-trash-alt" style="margin-right: 5px;"></i> Borrar</a>

                                <form id="edit-comment-form-<?php echo $comment->id_comentario ?>" style="display: none;" action="?option=show&item=<?php echo($_GET['item']);?>&action=edit&comment_id=<?php echo $comment->id_comentario; ?>" method="post">
                                    <textarea class="" id="edit-comment-field" name="comment" onkeypress="banWords(event);" onfocusout="censorBeep();" placeholder="Introduce tu comentario..."><?php echo $comment->contenido ?></textarea>
                                    <button class="new-comment-button" id="edit-comment-button" type="submit">Guardar edición</button>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
            <?php $comment_num += 1;
                } ?>
            </div>
            <!-- Nuevo comentario -->
            <?php if (isset($_SESSION['user_name'])) { ?>
                <div class="profile-img">
                    <img src="views/img/avatar/<?php echo($_SESSION['user_avatar']); ?>" alt="avatar">
                </div>
                <form class="comment-form" action="index.php?option=show&item=<?php echo($_GET['item']);?>&action=new-comment" method="post">
                    <textarea class="new-comment-text" id="comment-field" name="comment" onkeypress="banWords(event);" onfocusout="censorBeep();" placeholder="Introduce tu comentario..."></textarea>
                    <button class="new-comment-button" id="comment-button" type="submit" onclick="swapByLoadingIcon();">Comentar</button>
                </form>
            <?php }
            else {?>
                <div class="comment-denied">
                    <i class="far fa-comment" style="margin-right: 5px;"></i> <p>Por favor, <a href="?option=login&item=login">inicie sesión</a> o <a href="?option=login&item=registration">cree una cuenta</a> para poder comentar en artículos</p>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
