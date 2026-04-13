// js/share-functions.js - Share functionality

function shareDoodle(doodleId, username) {
    const currentUrl = window.location.origin + window.location.pathname;
    const message = `${username} sent you a doodle! Click the link to see it: ${currentUrl}`;
    
    // Copy to clipboard
    navigator.clipboard.writeText(message).then(() => {
        alert('✅ Link copied to clipboard!\n\n' + message);
    }).catch(err => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = message;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('✅ Link copied to clipboard!\n\n' + message);
    });
}

function copyDoodleLink(doodleId) {
    const doodleUrl = window.location.origin + '/pages/gallery/gallery.php?id=' + doodleId;
    
    navigator.clipboard.writeText(doodleUrl).then(() => {
        alert('✅ Doodle link copied!');
    }).catch(err => {
        alert('Failed to copy link');
    });
}