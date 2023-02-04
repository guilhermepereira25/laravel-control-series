import { Modal } from 'bootstrap'

const modal = document.getElementById('myModal')

const myModal = new Modal(modal, {
    keyboard: true,
})

modal.addEventListener('shows.bs.modal',  () => {
    myModal.show();
})
