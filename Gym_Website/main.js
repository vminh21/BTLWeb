if (history.scrollRestoration) {
    history.scrollRestoration = 'manual';
}

window.onload = function() {
    window.scrollTo(0, 0);
}
const menuBtn = document.getElementById("menu-btn");
const navLinks = document.getElementById("nav-links");
const menuBtnIcon = menuBtn.querySelector("i");
const bmiBtn = document.getElementById("btn");

menuBtn.addEventListener("click", (e) => {
    navLinks.classList.toggle("open");


    const isOpen = navLinks.classList.contains("open");
    menuBtnIcon.setAttribute("class", isOpen ? "ri-close-line" : "ri-menu-line");

});

navLinks.addEventListener('click', (e) => {
    navLinks.classList.remove('open');
    menuBtnIcon.setAttribute('class', 'ri-menu-line')

})

const scrollRevealOption = {
    distance : '50px',
    origin: 'bottom',
    duration: 1000,
};

const loginBtn = document.getElementById("btn-login");

if (loginBtn) {
    loginBtn.addEventListener("click", () => {
        const isLoggedIn = loginBtn.getAttribute("data-logged-in") === "true";
        if (isLoggedIn) {
            window.location.reload();
        } else {
            window.location.href = "Form_Login_Logout/login.php";
        }
    });
}

// sự kiên menu 
ScrollReveal().reveal(".header__content h1", {
    ...scrollRevealOption,
})
ScrollReveal().reveal(".header__content h2", {
    ...scrollRevealOption,
    delay: 500,
})
ScrollReveal().reveal(".header__content p", {
    ...scrollRevealOption,
    delay: 1000,
})
ScrollReveal().reveal(".header__content .header__btn", {
    ...scrollRevealOption,
    delay: 1500,
})
ScrollReveal().reveal(".about__card", {
    duration:1000,
    interval: 500,
})
ScrollReveal().reveal(".trainer__card", {
    ...scrollRevealOption,
    interval: 500,
})
ScrollReveal().reveal(".blog__card", {
    ...scrollRevealOption,
    interval: 500,
})
const swiper = new Swiper(".swiper", {
    loop: true,
  
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });  
  //Tính cân nặng chiều cao 
  const resetBtn = document.getElementById('reset_btn');
  resetBtn.addEventListener('click', function() {
    
    document.getElementById('height').value = '';
    document.getElementById('weight').value = '';
    if(document.getElementById('gender')) document.getElementById('gender').selectedIndex = 0;
    
    
    document.getElementById('height_error').innerHTML = '';
    document.getElementById('weight_error').innerHTML = '';
    
    if(document.getElementById('age')) {
        document.getElementById('age').value = '';
    }
    height_status = false;
    weight_status = false;
    
    
    resultArea.innerHTML = '/ Kết quả: <span>Chưa có dữ liệu</span>';
});
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
// Chat với admin

    var daXinSo = false;
    function toggleChat() {
        document.getElementById("chatBox").classList.toggle("active");
    }
    function selectOption(option) {
        addUserMessage(option);
        document.getElementById("chatOptions").style.display = 'none';
        
        setTimeout(function() {
            var botReply = getBotResponse(option);
            if (botReply) {
                addBotMessage(botReply);
            }
        }, 1000);
    }

    //  Gửi tin nhắn
    function sendMessage() {
        var input = document.getElementById("chatInput");
        var text = input.value.trim();
        
        if (text !== "") {
            addUserMessage(text);
            input.value = ""; 
            
            setTimeout(function() {
                var botReply = getBotResponse(text);
                if (botReply) {
                    addBotMessage(botReply);
                }
            }, 1000);
        }
    }

    function handleEnter(event) {
        if (event.key === "Enter") sendMessage();
    }

    function addUserMessage(text) {
        var chatBody = document.getElementById("chatBody");
        var userHtml = '<div class="message user-message"><p>' + text + '</p></div>';
        chatBody.insertAdjacentHTML('beforeend', userHtml);
        scrollToBottom();
    }

    function addBotMessage(text) {
        var chatBody = document.getElementById("chatBody");
        var botHtml = '<div class="message bot-message"><p>' + text + '</p></div>';
        chatBody.insertAdjacentHTML('beforeend', botHtml);
        scrollToBottom();
    }

    function scrollToBottom() {
        var chatBody = document.getElementById("chatBody");
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    // --- Chat Box ---
    function getBotResponse(input) {
        input = input.toLowerCase();

        // Kiểm tra từ khóa
        if (input.includes("tư vấn") || input.includes("sức khỏe")) {
            return "Để được tư vấn kỹ hơn, bạn cho mình xin chỉ số Chiều cao/Cân nặng nhé?";
        } 
        else if (input.includes("giá") || input.includes("tiền") || input.includes("chi phí")) {
            return "Bên mình có gói Standard (500k), Professional (1tr350) và Ultimate. Bạn muốn xem chi tiết gói nào?";
        } 
        else if (input.includes("gói tập") || input.includes("standard")) {
            return "Gói Standard 500k/tháng bao gồm Gym, Yoga và tủ đồ cá nhân ạ.";
        }
        else if (input.includes("địa chỉ") || input.includes("ở đâu")) {
            return "Phòng tập ở 123 Đường Chính, Sunrise Valley bạn nhé!";
        }
        else if (input.includes("chào") || input.includes("hi") || input.includes("hello")) {
            return "Chào bạn, chúc bạn một ngày tràn đầy năng lượng! Mình giúp gì được cho bạn?";
        }
        else if (input.includes("gặp nhân viên") || input.includes("nhân viên")) {
            return "Đã nhận yêu cầu. Bạn có thể gọi đến hotline để xin tư vấn: 0985772330";
        }
        else {
            if (daXinSo == false) {
                daXinSo = true; 
                return "Cảm ơn bạn đã nhắn tin. Hiện tại Admin đang bận, vui lòng để lại SĐT để bên mình gọi lại tư vấn nhé!";
            } else {
                return null; 
            }
        }
    }
  