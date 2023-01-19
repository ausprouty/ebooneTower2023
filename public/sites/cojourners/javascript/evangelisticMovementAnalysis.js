function showEvangelisticMovementAnalysis(){
    var storage = localStorage.getItem('evangelisticMovementAnalysis')
    if (storage) {
        var data = JSON.parse(storage)
        for (var i = 0; i < data.length; i++){
           console.log (data[i].row)
        }
    }
}

function updateChart(row, value){
    let storing = []
    let selected = {
        row: row,
    value: value,
    }
    found = false
    var storage = localStorage.getItem('evangelisticMovementAnalysis')
    if (storage) {
        var stored = JSON.parse(storage)
        if (stored !== null){
            for (var i = 0; i < stored.length; i++) {
                if (stored[i].row == row) {
                    storing[i].value = value
                    found = true
                } else {
                    storing[i] = stored[i]
                }
            }
        }
        if (found == false) {
            storing.push(selected)
        }
        localStorage.setItem('evangelisticMovementAnalysis', JSON.stringify(storing)) 
        document.getElementById('row' + row).className = 'col'+ value      
   }
}