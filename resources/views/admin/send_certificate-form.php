<h2 class="title">Publicar por correo electrónico</h2>
<form action="<?=admin_url('admin-post.php')?>" method="POST">
    <?php wp_nonce_field('send_certificate'); ?>
    <input type="hidden" name="action" value="send_certificate">
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="post_id">
                        Plantilla de Certificado
                    </label>
                </th>
                <td>
                    <select name="post_id" id="post_id">
                    <?php foreach ($templates as $template): ?>
                        <option value="<?=$template->ID?>">
                            <?=$template->post_title?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="user_id">
                        Usuario
                    </label>
                </th>
                <td>
                    <select name="user_id" id="user_id">
                    <?php foreach ($users as $user): ?>
                        <option value="<?=$user->ID?>">
                            <?=$user->display_name ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="submit">
        <input type="submit" class="button button-primary" value="Enviar correo">
    </p>
</form>