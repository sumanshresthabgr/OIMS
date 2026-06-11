function copyToClipboard(textToCopy, buttonId) {
    navigator.clipboard.writeText(textToCopy).then(() => {
        const targetBtn = document.getElementById(buttonId);
        const originalText = targetBtn.innerText;
        targetBtn.innerText = "Copied! ✓";
        targetBtn.style.background = "#16a34a";
        targetBtn.style.color = "#fff";
        
        setTimeout(() => {
            targetBtn.innerText = originalText;
            targetBtn.style.background = "";
            targetBtn.style.color = "";
        }, 2000);
    }).catch(err => {
        console.error('Could not copy link text: ', err);
    });
}