const selectAllCheckbox = document.getElementById('selectAllCheckbox');
const checkBoxes = document.querySelectorAll('.check');
function checkSelectAll() {
    let allChecked = true;
    checkBoxes.forEach(checkbox => {
    if (!checkbox.checked) {
    allChecked = false;
}
});
    selectAllCheckbox.checked = allChecked;
}

checkBoxes.forEach(checkbox => {
    checkbox.addEventListener('change', checkSelectAll);
});

selectAllCheckbox.addEventListener('change', function() {
    checkBoxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
});
