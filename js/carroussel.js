document.addEventListener('DOMContentLoaded', function() {
    const track = document.querySelector('.carousel-track');
    const items = document.querySelectorAll('.carousel-item');
    const prevButton = document.querySelector('.carousel-button.prev');
    const nextButton = document.querySelector('.carousel-button.next');
    
    if (!track || items.length === 0) return;
    
    let currentIndex = 0;
    const itemWidth = items[0].offsetWidth + 20; 
    const visibleItems = Math.floor(track.offsetWidth / itemWidth);
    
    track.style.width = `${items.length * itemWidth}px`;
    
    function moveCarousel(direction) {
      if (direction === 'next') {
        currentIndex++;
        
        if (currentIndex >= items.length - visibleItems + 1) {
          const transitionDuration = track.style.transitionDuration;
          track.style.transitionDuration = '0.5s';
          track.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
          
          setTimeout(() => {
            track.style.transitionDuration = '0s';
            currentIndex = 0;
            track.style.transform = `translateX(0px)`;
            
            setTimeout(() => {
              track.style.transitionDuration = transitionDuration;
            }, 50);
          }, 500);
          return;
        }
      } else if (direction === 'prev') {
        if (currentIndex > 0) {
          currentIndex--;
        } else {
          currentIndex = items.length - visibleItems;
        }
      }
      
      track.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
    }
    
    let autoScroll = setInterval(() => moveCarousel('next'), 5000);
    
    function resetInterval() {
      clearInterval(autoScroll);
      autoScroll = setInterval(() => moveCarousel('next'), 5000);
    }
    
    prevButton.addEventListener('click', () => {
      moveCarousel('prev');
      resetInterval();
    });
    
    nextButton.addEventListener('click', () => {
      moveCarousel('next');
      resetInterval();
    });
    
    track.addEventListener('mouseenter', () => clearInterval(autoScroll));
    track.addEventListener('mouseleave', () => {
      autoScroll = setInterval(() => moveCarousel('next'), 5000);
    });
    
    window.addEventListener('resize', function() {
      const newVisibleItems = Math.floor(track.offsetWidth / itemWidth);
      
      if (currentIndex > items.length - newVisibleItems) {
        currentIndex = items.length - newVisibleItems;
        track.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
      }
    });
  });