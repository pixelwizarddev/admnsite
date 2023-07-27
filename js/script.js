function showLoading() {
    document.getElementById('loading-overlay').style.display = 'block';
    document.getElementById('loading-spinner').style.display = 'block';
    document.getElementById('table-container').innerHTML = '';
    document.getElementById('error-message').textContent = '';
    document.getElementById('reset-button').style.display = 'none';
}

function hideLoading() {
    document.getElementById('loading-overlay').style.display = 'none';
    document.getElementById('loading-spinner').style.display = 'none';
    document.getElementById('reset-button').style.display = 'inline';
}

function getAdminUsernames() {
    const channelUsernames = document.getElementById('channel-username').value.trim();

    if (channelUsernames !== '') {
        showLoading();

        const request = new XMLHttpRequest();
        request.open('POST', 'get_admins.php', true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        request.onreadystatechange = function () {
            if (request.readyState === 4 && request.status === 200) {
                const response = request.responseText;
                document.getElementById('table-container').innerHTML = response;
                document.getElementById('error-message').textContent = '';
                hideLoading();
            } else if (request.readyState === 4) {
                document.getElementById('table-container').innerHTML = '';
                document.getElementById('error-message').textContent = 'Error fetching admin usernames.';
                hideLoading();
            }
        };

        request.send('channel_username=' + encodeURIComponent(channelUsernames));
    } else {
        document.getElementById('table-container').innerHTML = '';
        document.getElementById('error-message').textContent = 'Please enter channel usernames.';
    }
}

function resetForm() {
    document.getElementById('channel-username').value = '';
    document.getElementById('table-container').innerHTML = '';
    document.getElementById('error-message').textContent = '';
    document.getElementById('reset-button').style.display = 'none';
}

function copyUsernames(usernames) {
    const tempElement = document.createElement('textarea');
    const modifiedUsernames = usernames.replace(/@@/g, '@').replace(/Admin[^,@]+/g, '').replace(/,+/g, '\n').trim();
    tempElement.value = modifiedUsernames;
    document.body.appendChild(tempElement);
    tempElement.select();
    document.execCommand('copy');
    document.body.removeChild(tempElement);
}
