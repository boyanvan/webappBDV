filePath = "..\\files\\articlesDatabase.txt";

fetch(filePath)
    .then(response => response.text())
    .then(data => {
        loadArticles(data);
    });

function loadArticles(rawData) {
    let lines = rawData.split("\n")
    let linesCount = lines.length;

    for (let i = 0; i < linesCount; i += 4) {
        let title = lines[i].substring(7, lines[i].length);
        let text = lines[i + 1].substring(6, lines[i + 1].length);
        let author = lines[i + 2].substring(8, lines[i + 2].length);

        let articleHolder = document.getElementById("#articlesHolder");

        let article = document.createElement("article");

        let articleTitle = document.createElement("h3");
        let articleText = document.createElement("p");
        let articleAuthor = document.createElement("p");
        articleAuthor.className = "article__author";

        articleTitle.textContent = title;
        articleText.textContent = text;
        articleAuthor.textContent = author;

        article.appendChild(articleTitle);
        article.appendChild(articleText);
        article.appendChild(articleAuthor);

        article.addEventListener('click', () => {
            window.open("..\\..\\article.php");
        });

        articleHolder.appendChild(article);
    }
}