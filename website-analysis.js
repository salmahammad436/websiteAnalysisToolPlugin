document.addEventListener("DOMContentLoaded", function () {
  const analysisResultDiv = document.getElementById("website-analysis-result");
  const API_URL = `http://localhost:3001/api/websites/67bdd633cf67cdb70dfe7a4e`;
  fetch(API_URL, {
    method: "GET",
    mode: "cors",
    cache: "no-cache",
  })
    .then((response) => response.json())
    .then((data) => {
      let output = `<h3>${data.name}</h3>
      <a style='color:green;'>${data.url}</a>`
      if (data.AnalysisData && data.AnalysisData.length !== 0) {
        output += `<div class="analysis-container">`;
        data.AnalysisData.forEach((element) => {
          output += `
            <div class="analysis-card">
              <p><strong>SEO Score:</strong> ${handleScore(element.seoScore)}</p>
              <p><strong>Performance Score:</strong> ${handleScore(element.performanceScore)}</p>
              <p><strong>Accessibility Score:</strong> ${handleScore(element.accessibilityScore)}</p>
              <p><strong>Best Practice Score:</strong> ${handleScore(element.bestPracticeScore)}</p>
              <p><strong>Analysis Date:</strong> ${new Date(element.analysisDate).toLocaleString()}</p>
            </div>
          `;
        });
        output += `</div>`;
      } else {
        output += `<p class="error-text">Error getting data from this URL.</p>`;
      }

      analysisResultDiv.innerHTML = output;
    })
    .catch((error) => {
      console.error("Fetch error:", error);
      analysisResultDiv.innerHTML = `<p class="error-text">Error fetching data. Please check the API connection.</p>`;
    });
});

const handleScore = (num) => Math.round(num * 10) / 10;
