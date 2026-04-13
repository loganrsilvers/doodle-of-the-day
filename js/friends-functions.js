// js/friends-functions.js - Friends system functionality

function addFriend(friendId) {
    const formData = new FormData();
    formData.append('friend_id', friendId);
    
    fetch('handlers/add_friend.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add friend');
    });
}

function removeFriend(friendId) {
    if (!confirm('Remove this friend?')) return;
    
    const formData = new FormData();
    formData.append('friend_id', friendId);
    
    fetch('handlers/remove_friend.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to remove friend');
    });
}

function acceptFriend(requesterId) {
    const formData = new FormData();
    formData.append('requester_id', requesterId);
    
    fetch('handlers/accept_friend.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to accept friend');
    });
}

function rejectFriend(requesterId) {
    if (!confirm('Reject this friend request?')) return;
    
    const formData = new FormData();
    formData.append('requester_id', requesterId);
    
    fetch('handlers/reject_friend.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('❌ Failed to reject');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}