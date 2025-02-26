document.addEventListener("DOMContentLoaded", async function () {
  const analysisResultDiv = document.getElementById("website-analysis-result");
  if (!analysisResultDiv) return;
  try {
    const siteResponse = await fetch(websiteAnalysis.restUrl);
    if (!siteResponse.ok) {
      throw new Error(
        `Failed to fetch site ID. Status: ${siteResponse.status}`
      );
    }
    const siteData = await siteResponse.json();
    console.log("Fetched Site Data:", siteData);
    if (!siteData.id) {
      throw new Error("Invalid site ID received.");
    }
    const siteId = siteData.id;
    console.log("Site ID:", siteId);
    const API_URL = `http://localhost:3001/api/websites/${siteId}`;
    const response = await fetch(API_URL, {
      method: "GET",
      mode: "cors",
      cache: "no-cache",
    });
    if (!response.ok) {
      throw new Error(
        `Failed to fetch analysis data. Status: ${response.status}`
      );
    }
    const data = await response.json();
    analysisResultDiv.innerHTML = renderAnalysis(data);
  } catch (error) {
    console.error("Fetch error:", error);
    analysisResultDiv.innerHTML = `<p class="error-text">${error.message}</p>`;
  }
});
function renderAnalysis(data) {
  if (!data || !data.name || !data.url) {
    return `<p class="error-text">Invalid analysis data received.</p>`;
  }
  let output = `<h3>${data.name}</h3><a style='color:green;'>${data.url}</a>`;

  if (Array.isArray(data.AnalysisData) && data.AnalysisData.length > 0) {
    output += `<div class="analysis-container">`;
    data.AnalysisData.forEach((element) => {
      output += `
                <div class="analysis-card">
                    <p><strong>SEO Score:</strong> ${formatScore(
                      element.seoScore
                    )}</p>
                    <p><strong>Performance Score:</strong> ${formatScore(
                      element.performanceScore
                    )}</p>
                    <p><strong>Accessibility Score:</strong> ${formatScore(
                      element.accessibilityScore
                    )}</p>
                    <p><strong>Best Practice Score:</strong> ${formatScore(
                      element.bestPracticeScore
                    )}</p>
                    <p><strong>Analysis Date:</strong> ${new Date(
                      element.analysisDate
                    ).toLocaleString()}</p>
                </div>
            `;
    });
    output += `</div>`;
  } else {
    output += `<p class="error-text">No analysis data available.</p>`;
  }

  return output;
}

const formatScore = (num) => Math.round(num * 10) / 10;
