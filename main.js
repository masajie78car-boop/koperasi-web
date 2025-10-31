// Fungsi pencarian
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toLowerCase();
    const table = document.getElementById(tableId);
    const tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        tr[i].style.display = "none";
        const td = tr[i].getElementsByTagName("td");
        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                if (td[j].innerHTML.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                    break;
                }
            }
        }
    }
}

// Fungsi cetak PDF dari tabel
function printTableAsPDF(tableId, title) {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF();
    pdf.autoTable({ html: tableId });
    pdf.save(title + '.pdf');
}