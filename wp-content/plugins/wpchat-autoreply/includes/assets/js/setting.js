//user-search
        function filterFunction() {
            const input = document.getElementById('user-search');
            const filter = input.value.toUpperCase();
            const select = document.getElementById('user-select');
            const options = select.getElementsByTagName('option');
    
            for (const option of options) {
                const txtValue = option.text;
                option.style.display = txtValue.toUpperCase().includes(filter) ? '' : 'none';
            }
        }



