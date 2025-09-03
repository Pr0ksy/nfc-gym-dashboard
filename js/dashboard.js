document.addEventListener('DOMContentLoaded', function() {
    // --- MODAL ADD MEMBER ---
    const addMemberBtn = document.getElementById('addMemberBtn');
    const addMemberModal = document.getElementById('addMemberModal');
    const closeAddModal = document.getElementById('closeAddModal');
    const addMemberForm = document.getElementById('addMemberForm');
    const addMemberMessage = document.getElementById('addMemberMessage');

    addMemberBtn.addEventListener('click', () => { addMemberModal.style.display = 'flex'; });
    closeAddModal.addEventListener('click', () => { addMemberModal.style.display = 'none'; });
    window.addEventListener('click', e => { if(e.target == addMemberModal) addMemberModal.style.display='none'; });

    addMemberForm.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(addMemberForm);

        fetch('add_member.php', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(msg => {
                addMemberMessage.textContent = msg;
                if(msg.toLowerCase().includes('uspešno')){
                    addMemberForm.reset();
                    setTimeout(() => location.reload(), 1000);
                }
            });
    });

    // --- MODAL VIEW/EDIT MEMBER ---
    const modal = document.getElementById('memberModal');
    const closeModal = document.getElementById('closeModal');
    const modalContent = document.getElementById('modalContent');

    document.querySelector('table').addEventListener('click', function(e){
        const row = e.target.closest('.member-row');
        if(!row) return; // nije klik na red
        const memberId = row.getAttribute('data-id');

        fetch('get_member.php?id=' + memberId)
            .then(res => res.text())
            .then(data => {
                modalContent.innerHTML = data;
                modal.style.display = 'flex';

                // OBNOVA ČLANARINE
                const renewForm = modalContent.querySelector('#renewForm');
                if (renewForm) {
                    renewForm.addEventListener('submit', function(e){
                        e.preventDefault();
                        const formData = new FormData(this);
                        const memberId = formData.get('id');

                        fetch('renew_membership.php', { method:'POST', body:formData })
                        .then(res => res.text())
                        .then(msg => {
                            modalContent.querySelector('#renewMessage').innerHTML = msg;
                            const newDateMatch = msg.match(/\d{4}-\d{2}-\d{2}/);
                            if(newDateMatch){
                                const newDate = newDateMatch[0];
                                modalContent.querySelector('#memberDate').textContent = newDate;
                                modalContent.querySelector('#memberStatus').textContent = "✅ Aktivna";

                                const tableRow = document.querySelector('tr[data-id="'+memberId+'"]');
                                if(tableRow){
                                    tableRow.querySelector('td:nth-child(6)').textContent = newDate;
                                    tableRow.querySelector('td:nth-child(7)').textContent = "✅ Aktivna";
                                }
                            }
                        });
                    });
                }

                // BRISANJE ČLANA
                const deleteBtn = modalContent.querySelector('#deleteBtn');
                if(deleteBtn){
                    deleteBtn.addEventListener('click', function(){
                        if(confirm("Da li si siguran da želiš da obrišeš ovog člana?")){
                            const memberId = modalContent.querySelector('input[name="id"]').value;
                            fetch('delete_member.php?id='+memberId)
                                .then(res => res.text())
                                .then(msg => {
                                    alert(msg);
                                    const row = document.querySelector('tr[data-id="'+memberId+'"]');
                                    if(row) row.remove();
                                    modal.style.display = 'none';
                                });
                        }
                    });
                }
            });
    });

    // zatvaranje modala
    closeModal.addEventListener('click', () => { modal.style.display = 'none'; });
    window.addEventListener('click', e => { if(e.target==modal) modal.style.display='none'; });

});
