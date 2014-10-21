<?php
   // PDO For sqlite3. Database file will be stored in the assets folder
   class comment {
      private $dbh;
      const DB_PATH = "<PATH-TO-DB-FILE-OR-CONNECTION-DETAILS";

      function comment() {
      // REQUIRES: 
      // MODIFIES: comment::$dbh
      // EFFECTS: Establishes new PDO object (database connection) if
      //          appropriate credentials entered, dies otherwise 
         try {
            $this->dbh = new PDO("sqlite:" . self::DB_PATH);  // Sqlite3 is used here. Mysql or PostgreSQL might be more apropriate.
         } catch(PDOException $e) {
            die("Could not connect to db at: " . self::DB_PATH . "\nError: ". $e.message); 
         }
      }

      function insert_comment($post_id, $author, $comment) {
      // REQUIRES: 
      // MODIFIES: 
      // EFFECTS: Inserts a new comment with the specified author & comment,
      //          Returns true if successful, false otherwise.
         $post_id = trim($post_id);
         $author = trim($author);
         $comment = trim($comment);
         try {
            $insert_stmt = $this->dbh->prepare("INSERT or IGNORE INTO comment (comment_post_id, comment_username, comment_content) VALUES (:post_id, :username, :content)"); 
            $insert_stmt->bindParam(':post_id', $post_id);
            $insert_stmt->bindParam(':username', $author);
            $insert_stmt->bindParam(':content', $comment); 
            $insert_stmt->execute();
         } catch(PDOException $e) {
            die("Failed! " . $e.message);
            return false;
         }
         return true;
      }

      function retrieve_comments($post_id)
      {
      // REQUIRES:
      // MODIFIES:
      // EFFECTS: Returns a multidimensional associative array of comments 
      //          if successful, false otherwise 
         $results;
         try {
            $fetch_stmt = $this->dbh->prepare("SELECT comment_username, comment_content FROM comment where comment_post_id = :post_id");
            $fetch_stmt->bindParam(':post_id', $post_id);
            $fetch_stmt->execute();
            $results = $fetch_stmt->fetchAll();
         } catch(PDOException $e) {
            die("failed: ". $e.message);

         }
         return $results;
      }
   };

?>




<?php
// Actual Comment Functionality
// AJAX is not supported, and the use of fieldset is not dynamic to website size
$commenthandle = new comment;
?>

<?php
if(isset($_POST['addcomment'])) {
   if($commenthandle->insert_comment($_POST['post_id'], $_POST['author'], $_POST['comment'])) {
      echo "Comment posted!<br />";
   } else {
      echo "Posting failed!<br />";
   }
}
?>
<fieldset style="width: 100%; padding: 10px;">
<legend><h2>Comments</h2></legend>

<?php
$post_id = "{{ page.id }}";
if($comments = $commenthandle->retrieve_comments($post_id)) {
   if(is_array($comments)) {
      if(count($comments) > 0) {


         foreach($comments as $comment) {
            echo "<p style=\"text-align:left\"><strong>" . $comment['comment_username'] . "</strong>:&nbsp;" . "<br /> " . $comment['comment_content'] . "</p>";
         }


      } else {
         echo "No comments.<br />";
      }
   } else {
      echo "There's a problem!<br />";
   }
} else {
echo "No comments.<br />";
}
?>
</fieldset>


<p>Add a Comment</p>
<form action="#comments" method="post">
<input type="text" name="author" style="width: 100%" placeholder="Name" /><br />
<textarea placeholder="Comment" style="width: 100%; height: 100px;" name="comment"></textarea><br />
<input type="hidden" name="post_id" value="{{ page.id }}">
<input type="submit" id="comment-submit" value="Submit" name="addcomment" />
</form>
