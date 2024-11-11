filePath = "..\\files\\rise_of_robotics.txt";

fetch(filePath)
    .then(response => response.text())
    .then(data => {
        generateArticle(data);
    });

function generateArticle(rawData) {
    let lines = rawData.split('\n');
    let linesCount = lines.length;

    let startIndex = lines.indexOf("-START-");
    let endIndex = lines.indexOf("-END-");

    document.getElementById("#article__title").textContent = lines[0].substring(7, lines[0].length);
    document.getElementById("#article__author").textContent = "Author: " + lines[1].substring(8, lines[1].length);

    let text = "";

    for (let i = 3; i < endIndex; i++){
        text += lines[i] + "\n";
    }

    document.getElementById("#article__content").textContent = text;
}