function handleRenameInput(input) {
    const form = input.closest('form');
    const deleteButton = form.querySelector('button[name="delPlaylist"]');

    if (input.value.trim() !== "") {
        deleteButton.style.display = 'none';
    } else {
        deleteButton.style.display = 'inline';
    }
}

function validateNameChangeForm(event) {
    const form = event.target;
    const renameField = form.querySelector('input[name="renamePlaylist"]');
    const deleteButton = form.querySelector('button[name="delPlaylist"]');

    if (event.submitter === deleteButton) {
        return confirm('Are you sure you want to delete this playlist?');
    }

    if (renameField.value.trim() === "") {
        alert("The rename field cannot be empty.");
        event.preventDefault();
        return false;
    }
    return true;
}

function deletePlaylist(button) {
    const form = button.closest('form');
    const confirmDelete = confirm('Are you sure you want to delete this playlist?');
    
    if (confirmDelete) {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'delPlaylist';
        hiddenInput.value = '1';
        form.appendChild(hiddenInput);
        form.submit();
    }
}
