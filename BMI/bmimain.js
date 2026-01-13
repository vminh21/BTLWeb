  //Tính cân nặng chiều cao 
  bmiBtn.addEventListener("click", () => {
    const height = parseFloat(document.getElementById("height").value);
    const weight = parseFloat(document.getElementById("weight").value);
    const result = document.getElementById("output");
    let height_status = false, weight_status = false;

    if (isNaN(height) || height <= 0) {
        document.getElementById('height_error').innerHTML = 'Chiều cao không hợp lệ';
    } else {
        document.getElementById('height_error').innerHTML = '';    
        height_status = true;
    }
    
    if (isNaN(weight) || weight <= 0) {
        document.getElementById('weight_error').innerHTML = 'Cân nặng không hợp lệ';
    } else {
        document.getElementById('weight_error').innerHTML = '';    
        weight_status = true;
    }

    if (height_status && weight_status) {
        const bmi = (weight / ((height * height) / 10000)).toFixed(2);
        let category = "";
        if (bmi < 18.5) category = "Gầy";
        else if (bmi <= 24.9) category = "Bình thường";
        else if (bmi <= 29.9) category = "Tiền béo phì";
        else category = "Béo phì";

        result.innerHTML = `/ Kết quả: <span>${bmi} (${category})</span>`;
    } else {
        alert('Vui lòng kiểm tra lại thông tin!');
        result.innerHTML = '';
    }
});