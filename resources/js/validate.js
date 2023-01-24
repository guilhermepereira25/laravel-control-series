function validateField() {
    let answer = alert("Deseja mesmo excluir esse registro? ");

    if (!answer) {
        return false;
    } 

    return true;
}