var articles = document.getElementsByName("article");

articles.forEach(article => {
    article.addEventListener('click', () => {
        window.open("article.php");
        console.log("beep");
    })
});