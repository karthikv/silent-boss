<h1>Add Page</h1>
<p>You may add a page by using the form below.</p>

<form method="post" action="<?= Util::getConfig( 'url_root' ) ?>/admin/add-page-handler">
   <p>
      <label for="title">Title</label>
      <input type="text" name="title"></input>
   </p>
   
   <p>
      <label for="url">URL</label>
      <input type="text" name="url"></input>
   </p>

   <p>
      <label for="text">Contents (Markdown)</label>
      <textarea name="text" rows="8" cols="40"></textarea>
   </p>

   <input type="submit" class="input-submit" value="Add post!"></input>
</form>

