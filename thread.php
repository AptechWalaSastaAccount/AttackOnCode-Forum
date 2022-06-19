<?php 
    session_start();
    require "./partials/_connection.php";

    $showAlert = false;

    $thread_id = $_GET['thread_id'];

    $threadFetchingSql = "SELECT * FROM `threads` WHERE thread_id = '$thread_id'";
    $threadFetchingResult = mysqli_query($conn, $threadFetchingSql);
    $threadFetchingArray = mysqli_fetch_assoc($threadFetchingResult);

    $user_id = $threadFetchingArray['thread_user_id'];
    
    $usernameFetchingSql = "SELECT * FROM `users` WHERE user_id = '$user_id'";
    $usernameFetchingResult = mysqli_query($conn, $usernameFetchingSql);
    $usernameFetchingArray = mysqli_fetch_assoc($usernameFetchingResult);

    // print_r($usernameFetchingArray['user_name']);

    // FUNCTIONALITY FOR ADDING COMMENTS

    $comment_description = $errorMsgComment = "";

    if(isset($_SESSION['user_id'])){
        $user_idC = $_SESSION['user_id'];
    }


    if(isset($_POST['addComment'])){
        if(!empty($_POST['comment_description'])){
            $comment_description = mysqli_real_escape_string($conn, $_POST['comment_description']);
            $comment_code = mysqli_real_escape_string($conn, $_POST['comment_code']);
    
            $commentAddingSql = "INSERT INTO `comments` (`comment_content`, `comment_code`, `thread_id`, `user_id`) VALUES ('$comment_description', '$comment_code', '$thread_id', '$user_idC');";
            $commentAddingResult = mysqli_query($conn, $commentAddingSql);

            if($commentAddingResult){
                $showAlert = true;
            }
        }else {
            $errorMsgComment = "Enter a comment";
        }

    }

    $commentsFetchingSql = "SELECT * FROM `comments` WHERE thread_id = '$thread_id'";
    $commentsFetchingResult = mysqli_query($conn, $commentsFetchingSql);
    $commentsFetchingArray = mysqli_fetch_all($commentsFetchingResult, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./css/navbar.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="./css/thread.css">
    <link rel="stylesheet" href="/path/to/styles/default.min.css">
    <link rel="stylesheet"
      href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.1/styles/base16/dracula.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.1/highlight.min.js"></script>
    <script src="/path/to/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>
    <title>AttackOnCode - Thread</title>
</head>
<body>
    <?php include "./partials/_navbar.php" ?>
    <?php if($showAlert): ?>
    <div class="alert-box success">
        <p class="alert-msg"> <strong> Success!!</strong>  Your comment has been added successfully</p>
    </div>
    <?php endif; ?>
    <main>
        <section id="thread">
            <div class="thread-container">
            <div class="thread" href="thread.php">
                    <h3 class="thread-heading"><?php echo htmlspecialchars($threadFetchingArray['thread_title']) ?></h3>
                    <p class="thread-desc"><?php echo htmlspecialchars($threadFetchingArray['thread_desc']) ?></p>
                    <?php if($threadFetchingArray['thread_code'] != ""): ?>
                    <pre style="margin-bottom: 1rem;font-size: 1.2rem"><code><?php echo htmlspecialchars($threadFetchingArray['thread_code']) ?></code></pre>
                    <?php endif; ?>
                    <p><strong class="username-heading">Posted By : </strong> <span class="username"><?php echo htmlspecialchars($usernameFetchingArray['user_name']) ?></span></p>
                </div>
            </div>
        </section>
        <?php if(isset($_SESSION['loggedin'])): ?>
        <section class="add-comment">
            <h2 class="add-comment-heading">Add a Comment</h2>
            <div class="add-comment-container">
                <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="POST" class="add-comment-form">
                    <textarea name="comment_description" placeholder="Add a Comment ........." class="comment-description" id="comment-description" cols="30" rows="5"></textarea>
                    <span class="mb-2"><?php echo htmlspecialchars($errorMsgComment) ?></span>
                    <textarea style="margin-bottom: 2rem;" name="comment_code" placeholder="Paste your code here" class="comment-code" id="comment-code" cols="30" rows="5"></textarea>
                    <input type="submit" value="Add Comment" name="addComment" class="add-comment-btn">
                </form>
            </div>
        </section>
        <?php else: ?>
        <section id="nothing">
            <div class="nothing-box">
                <p>You need to be logged in to add a comment ! <a style="text-decoration: underline; cursor: pointer;" href="login.php">Click here</a> to Login</p>
            </div>
        </section>
        <?php endif; ?>
        <section id="commentlist">
            <h2 class="commentlist-heading">Browse Comments</h2>
            <div class="commentlist-container">
                <?php if($commentsFetchingArray): ?>
                <?php foreach($commentsFetchingArray as $comments => $comment): ?>
                <?php 
                    $cuser_id = $comment['user_id'];
                    $cusernameFetchingSql = "SELECT * FROM `users` WHERE user_id = '$cuser_id'";
                    $cusernameFetchingResult = mysqli_query($conn, $cusernameFetchingSql);
                    $cusernameFetchingArray = mysqli_fetch_assoc($cusernameFetchingResult);   
                ?>
                <div class="comment" href="thread.php">
                    <h3 class="comment-heading"><?php echo htmlspecialchars($cusernameFetchingArray['user_name']) ?></h3>
                    <p class="comment-desc"><?php echo htmlspecialchars($comment['comment_content']) ?></p>
                    <?php if($comment['comment_code'] != ""): ?>
                        <pre style="margin: 1rem 0;font-size: 1.2rem"><code><?php echo htmlspecialchars($comment['comment_code']) ?></code></pre>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                    <section id="nothing">
                        <div class="nothing-box">
                            <p>No comments are available for this thread</p>
                        </div>
                   </section>
                <?php endif;?>
            </div>
        </section>
    </main>
    <?php include "./partials/_footer.php" ?>


    <script src="./script/app.js"></script>
</body>
</html>