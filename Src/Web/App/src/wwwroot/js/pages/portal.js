import { topNavigation } from "../components/top-navigation";
import { $ } from "../core/dom";

let topNav = $(".top-navigation");
let content = $(".page");

if (topNav && content) {
    topNav = new topNavigation(topNav, content);
}

import Chart from 'chart.js/auto'

const studentCount = document.getElementById('studentCount').innerText;
const coordinatorsCount = document.getElementById('coordinatorsCount').innerText;

const pieChartdata = {
    labels: [
        'Students',
        'Partners',
        'PDC Assistant Cordinators',
    ],
    datasets: [{
        label: '',
        data: [studentCount, 30, coordinatorsCount],
        backgroundColor: [
            '#d2d3d4',
            '#f2f7ff',
            '#00589b',
        ],
        hoverOffset: 4
    }]
};

const pieChartconfig = {
    type: 'pie',
    data: pieChartdata,
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Users Count'
            },
            legend: {
                position: 'right',
                align: 'center',
            },
        },
    },
};

const barChartData = {
    labels: ['Approved Partners', 'Pending Partners'],
    datasets: [{
        label: 'Partners Count',
        data: [30, 27],
        backgroundColor: '#00589b',
        borderColor: '#f2f7ff',
        borderWidth: 1,
        barThickness: 25
    }]
};

const barChartConfig = {
    type: 'bar',
    data: barChartData,
    options: {
        indexAxis: 'y',
    }
};


// Create the chart
new Chart(
    document.getElementById('piechart'),
    pieChartconfig
);

const barChart = new Chart(
    document.getElementById('barchart'),
    barChartConfig
);


function animateCounting(element, startCount, endCount, duration) {
    let startTime = null;

    function step(timestamp) {
        if (!startTime) startTime = timestamp;
        const progress = timestamp - startTime;
        const percentage = Math.min(progress / duration, 1);

        const count = Math.floor(startCount + percentage * (endCount - startCount));
        element.textContent = count;

        if (percentage < 1) {
            window.requestAnimationFrame(step);
        }
    }

    window.requestAnimationFrame(step);
}

// Function to handle intersection observer callback
function handleIntersection(entries, observer) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const countElement = entry.target.querySelector('h3');
            const startCount = 0;
            const endCount = parseInt(countElement.textContent);
            const duration = 1000;
            animateCounting(countElement, startCount, endCount, duration);
            observer.unobserve(entry.target);
        }
    });
}


const observer = new IntersectionObserver(handleIntersection);


const cards = document.querySelectorAll('.dash-container .card');
cards.forEach(card => {
    observer.observe(card);
});




