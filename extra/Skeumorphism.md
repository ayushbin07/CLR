##Use these 
Dont copy paste blindly but tthis. You may chnage the dimension but not the look.

##For Buttons.
<!-- From Uiverse.io by FColombati --> 
<button class="button">
  <div class="button-outer">
    <div class="button-inner">
      <span>Press me</span>
    </div>
  </div>
</button>
/* From Uiverse.io by FColombati */ 
.button {
  all: unset;
  cursor: pointer;
  -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
  position: relative;
  border-radius: 100em;
  background-color: rgba(0, 0, 0, 0.75);
  box-shadow:
    -0.15em -0.15em 0.15em -0.075em rgba(5, 5, 5, 0.25),
    0.0375em 0.0375em 0.0675em 0 rgba(5, 5, 5, 0.1);
}

.button::after {
  content: "";
  position: absolute;
  z-index: 0;
  width: calc(100% + 0.3em);
  height: calc(100% + 0.3em);
  top: -0.15em;
  left: -0.15em;
  border-radius: inherit;
  background: linear-gradient(
    -135deg,
    rgba(5, 5, 5, 0.5),
    transparent 20%,
    transparent 100%
  );
  filter: blur(0.0125em);
  opacity: 0.25;
  mix-blend-mode: multiply;
}

.button .button-outer {
  position: relative;
  z-index: 1;
  border-radius: inherit;
  transition: box-shadow 300ms ease;
  will-change: box-shadow;
  box-shadow:
    0 0.05em 0.05em -0.01em rgba(5, 5, 5, 1),
    0 0.01em 0.01em -0.01em rgba(5, 5, 5, 0.5),
    0.15em 0.3em 0.1em -0.01em rgba(5, 5, 5, 0.25);
}

.button:hover .button-outer {
  box-shadow:
    0 0 0 0 rgba(5, 5, 5, 1),
    0 0 0 0 rgba(5, 5, 5, 0.5),
    0 0 0 0 rgba(5, 5, 5, 0.25);
}

.button-inner {
  --inset: 0.035em;
  position: relative;
  z-index: 1;
  border-radius: inherit;
  padding: 1em 1.5em;
  background-image: linear-gradient(
    135deg,
    rgba(230, 230, 230, 1),
    rgba(180, 180, 180, 1)
  );
  transition:
    box-shadow 300ms ease,
    clip-path 250ms ease,
    background-image 250ms ease,
    transform 250ms ease;
  will-change: box-shadow, clip-path, background-image, transform;
  overflow: clip;
  clip-path: inset(0 0 0 0 round 100em);
  box-shadow:
        /* 1 */
    0 0 0 0 inset rgba(5, 5, 5, 0.1),
    /* 2 */ -0.05em -0.05em 0.05em 0 inset rgba(5, 5, 5, 0.25),
    /* 3 */ 0 0 0 0 inset rgba(5, 5, 5, 0.1),
    /* 4 */ 0 0 0.05em 0.2em inset rgba(255, 255, 255, 0.25),
    /* 5 */ 0.025em 0.05em 0.1em 0 inset rgba(255, 255, 255, 1),
    /* 6 */ 0.12em 0.12em 0.12em inset rgba(255, 255, 255, 0.25),
    /* 7 */ -0.075em -0.25em 0.25em 0.1em inset rgba(5, 5, 5, 0.25);
}

.button:hover .button-inner {
  clip-path: inset(
    clamp(1px, 0.0625em, 2px) clamp(1px, 0.0625em, 2px)
      clamp(1px, 0.0625em, 2px) clamp(1px, 0.0625em, 2px) round 100em
  );
  box-shadow:
        /* 1 */
    0.1em 0.15em 0.05em 0 inset rgba(5, 5, 5, 0.75),
    /* 2 */ -0.025em -0.03em 0.05em 0.025em inset rgba(5, 5, 5, 0.5),
    /* 3 */ 0.25em 0.25em 0.2em 0 inset rgba(5, 5, 5, 0.5),
    /* 4 */ 0 0 0.05em 0.5em inset rgba(255, 255, 255, 0.15),
    /* 5 */ 0 0 0 0 inset rgba(255, 255, 255, 1),
    /* 6 */ 0.12em 0.12em 0.12em inset rgba(255, 255, 255, 0.25),
    /* 7 */ -0.075em -0.12em 0.2em 0.1em inset rgba(5, 5, 5, 0.25);
}

.button .button-inner span {
  position: relative;
  z-index: 4;
  font-family: "Inter", sans-serif;
  letter-spacing: -0.05em;
  font-weight: 500;
  color: rgba(0, 0, 0, 0);
  background-image: linear-gradient(
    135deg,
    rgba(25, 25, 25, 1),
    rgba(75, 75, 75, 1)
  );
  -webkit-background-clip: text;
  background-clip: text;
  transition: transform 250ms ease;
  display: block;
  will-change: transform;
  text-shadow: rgba(0, 0, 0, 0.1) 0 0 0.1em;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

.button:hover .button-inner span {
  transform: scale(0.975);
}

.button:active .button-inner {
  transform: scale(0.975);
}



##For toggle
<!-- From Uiverse.io by njesenberger --> 
<div class="toggle-wrapper">
  <input class="toggle-checkbox" type="checkbox">
  <div class="toggle-container">  
    <div class="toggle-button">
      <div class="toggle-button-circles-container">
        <div class="toggle-button-circle"></div>
        <div class="toggle-button-circle"></div>
        <div class="toggle-button-circle"></div>
        <div class="toggle-button-circle"></div>
        <div class="toggle-button-circle"></div>
        <div class="toggle-button-circle"></div>
        <div class="toggle-button-circle"></div>
        <div class="toggle-button-circle"></div>
        <div class="toggle-button-circle"></div>
        <div class="toggle-button-circle"></div>
        <div class="toggle-button-circle"></div>
        <div class="toggle-button-circle"></div>
      </div>
    </div>
  </div>
</div>
/* From Uiverse.io by njesenberger */ 
.toggle-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  border-radius: .5em;
  padding: .125em;
  background-image: linear-gradient(to bottom, #d5d5d5, #e8e8e8);
  box-shadow: 0 1px 1px rgb(255 255 255 / .6);
  /* resize for demo */
  font-size: 1.5em;
}

.toggle-checkbox {
  appearance: none;
  position: absolute;
  z-index: 1;
  border-radius: inherit;
  width: 100%;
  height: 100%;
  /* fix em sizing */
  font: inherit;
  opacity: 0;
  cursor: pointer;
}

.toggle-container {
  display: flex;
  align-items: center;
  position: relative;
  border-radius: .375em;
  width: 3em;
  height: 1.5em;
  background-color: #e8e8e8;
  box-shadow: inset 0 0 .0625em .125em rgb(255 255 255 / .2), inset 0 .0625em .125em rgb(0 0 0 / .4);
  transition: background-color .4s linear;
}

.toggle-checkbox:checked + .toggle-container {
  background-color: #f3b519;
}

.toggle-button {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  left: .0625em;
  border-radius: .3125em;
  width: 1.375em;
  height: 1.375em;
  background-color: #e8e8e8;
  box-shadow: inset 0 -.0625em .0625em .125em rgb(0 0 0 / .1), inset 0 -.125em .0625em rgb(0 0 0 / .2), inset 0 .1875em .0625em rgb(255 255 255 / .3), 0 .125em .125em rgb(0 0 0 / .5);
  transition: left .4s;
}

.toggle-checkbox:checked + .toggle-container > .toggle-button {
  left: 1.5625em;
}

.toggle-button-circles-container {
  display: grid;
  grid-template-columns: repeat(3, min-content);
  gap: .125em;
  position: absolute;
  margin: 0 auto;
}

.toggle-button-circle {
  border-radius: 50%;
  width: .125em;
  height: .125em;
  background-image: radial-gradient(circle at 50% 0, #f5f5f5, #c4c4c4);
}

##For checkboxes
<!-- From Uiverse.io by adamgiebl --> 
<label class="checkbox" for="checkbox1">
  <span class="label">Checkbox</span>
  <input checked="" id="checkbox1" type="checkbox">
  <span class="checkmark"></span>
</label>
/* From Uiverse.io by adamgiebl */ 
.checkbox {
  display: flex;
  align-items: center;
  margin: 10px;
  font-family: Arial, sans-serif;
  color: black;
}

.checkbox input {
  display: none;
}

.checkbox .checkmark {
  width: 28px;
  height: 28px;
  border-radius: 10px;
  background-color: #ffffff2b;
  box-shadow: rgba(0, 0, 0, 0.62) 0px 0px 5px inset, rgba(0, 0, 0, 0.21) 0px 0px 0px 24px inset,
        #22cc3f 0px 0px 0px 0px inset, rgba(224, 224, 224, 0.45) 0px 1px 0px 0px;
  cursor: pointer;
  position: relative;
}

.checkbox .checkmark::after {
  content: "";
  width: 18px;
  height: 18px;
  border-radius: 5px;
  background-color: #e3e3e3;
  box-shadow: transparent 0px 0px 0px 2px, rgba(0, 0, 0, 0.3) 0px 6px 6px;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  transition: background-color 0.3s ease-in-out;
}

.checkbox input:checked + .checkmark {
  background-color: #22cc3f;
  box-shadow: rgba(0, 0, 0, 0.62) 0px 0px 5px inset, #22cc3f 0px 0px 0px 2px inset, #22cc3f 0px 0px 0px 24px inset,
        rgba(224, 224, 224, 0.45) 0px 1px 0px 0px;
}

.checkbox input:checked + .checkmark::after {
  background-color: white;
}

.checkbox .label {
  margin-right: 10px;
  user-select: none;
  font-weight: 700;
  cursor: pointer;
}

##For Image card
<!-- From Uiverse.io by LilaRest --> 
<div class="card">
  <div class="card-overlay"></div>
  <div class="card-inner">YOUR<br>CONTENT<br>HERE</div>
</div>
/* From Uiverse.io by LilaRest */ 
.card {
  --bg: #e8e8e8;
  --contrast: #e2e0e0;
  --grey: #93a1a1;
  position: relative;
  padding: 9px;
  background-color: var(--bg);
  border-radius: 35px;
  box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px, rgba(10, 37, 64, 0.35) 0px -2px 6px 0px inset;
}

.card-overlay {
  position: absolute;
  inset: 0;
  pointer-events: none;
  background: repeating-conic-gradient(var(--bg) 0.0000001%, var(--grey) 0.000104%) 60% 60%/600% 600%;
  filter: opacity(10%) contrast(105%);
}

.card-inner {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
  width: 190px;
  height: 254px;
  background-color: var(--contrast);
  border-radius: 30px;
  /* Content style */
  font-size: 30px;
  font-weight: 900;
  color: #c7c4c4;
  text-align: center;
  font-family: monospace;
}

##For loading screen (May add later)
<!-- From Uiverse.io by Praashoo7 --> 
<div class="main">
  <div class="up">
    <div class="loaders">
      <div class="loader"></div>
      <div class="loader"></div>
      <div class="loader"></div>
      <div class="loader"></div>
      <div class="loader"></div>
      <div class="loader"></div>
      <div class="loader"></div>
      <div class="loader"></div>
      <div class="loader"></div>
      <div class="loader"></div>
    </div>
    <div class="loadersB">
      <div class="loaderA">
        <div class="ball0"></div>
      </div>
      <div class="loaderA">
        <div class="ball1"></div>
      </div>
      <div class="loaderA">
        <div class="ball2"></div>
      </div>
      <div class="loaderA">
        <div class="ball3"></div>
      </div>
      <div class="loaderA">
        <div class="ball4"></div>
      </div>
      <div class="loaderA">
        <div class="ball5"></div>
      </div>
      <div class="loaderA">
        <div class="ball6"></div>
      </div>
      <div class="loaderA">
        <div class="ball7"></div>
      </div>
      <div class="loaderA">
        <div class="ball8"></div>
      </div>
    </div>
  </div>
</div>
/* From Uiverse.io by Praashoo7 */ 
.main {
  display: flex;
  align-items: center;
  justify-content: center;
}

.loaders,
.loadersB {
  display: flex;
  align-items: center;
  justify-content: center;
}

.loader {
  position: absolute;
  width: 1.15em;
  height: 13em;
  border-radius: 50px;
  background: #e0e0e0;
}
.loader:after {
  content: "";
  position: absolute;
  left: 0;
  top: 0;
  width: 1.15em;
  height: 5em;
  background: #e0e0e0;
  border-radius: 50px;
  border: 1px solid #e2e2e2;
  box-shadow:
    inset 5px 5px 15px #d3d2d2ab,
    inset -5px -5px 15px #e9e9e9ab;
  mask-image: linear-gradient(
    to bottom,
    black calc(100% - 48px),
    transparent 100%
  );
}
.loader::before {
  content: "";
  position: absolute;
  bottom: 0;
  right: 0;
  width: 1.15em;
  height: 4.5em;
  background: #e0e0e0;
  border-radius: 50px;
  border: 1px solid #e2e2e2;
  box-shadow:
    inset 5px 5px 15px #d3d2d2ab,
    inset -5px -5px 15px #e9e9e9ab;
  mask-image: linear-gradient(
    to top,
    black calc(100% - 48px),
    transparent 100%
  );
}
.loaderA {
  position: absolute;
  width: 1.15em;
  height: 13em;
  border-radius: 50px;
  background: transparent;
}
.ball0,
.ball1,
.ball2,
.ball3,
.ball4,
.ball5,
.ball6,
.ball7,
.ball8,
.ball9 {
  width: 1.15em;
  height: 1.15em;
  box-shadow:
    rgba(0, 0, 0, 0.17) 0px -10px 10px 0px inset,
    rgba(0, 0, 0, 0.15) 0px -15px 15px 0px inset,
    rgba(0, 0, 0, 0.1) 0px -40px 20px 0px inset,
    rgba(0, 0, 0, 0.06) 0px 2px 1px,
    rgba(0, 0, 0, 0.09) 0px 4px 2px,
    rgba(0, 0, 0, 0.09) 0px 8px 4px,
    rgba(0, 0, 0, 0.09) 0px 16px 8px,
    rgba(0, 0, 0, 0.09) 0px 32px 16px,
    0px -1px 15px -8px rgba(0, 0, 0, 0.09);
  border-radius: 50%;
  transition: transform 800ms cubic-bezier(1, -0.4, 0, 1.4);
  background-color: rgb(232, 232, 232, 1);
  animation: 3.63s move ease-in-out infinite;
}
.loader:nth-child(2) {
  transform: rotate(20deg);
}
.loader:nth-child(3) {
  transform: rotate(40deg);
}
.loader:nth-child(4) {
  transform: rotate(60deg);
}
.loader:nth-child(5) {
  transform: rotate(80deg);
}
.loader:nth-child(6) {
  transform: rotate(100deg);
}
.loader:nth-child(7) {
  transform: rotate(120deg);
}
.loader:nth-child(8) {
  transform: rotate(140deg);
}
.loader:nth-child(9) {
  transform: rotate(160deg);
}

.loaderA:nth-child(2) {
  transform: rotate(20deg);
}
.loaderA:nth-child(3) {
  transform: rotate(40deg);
}
.loaderA:nth-child(4) {
  transform: rotate(60deg);
}
.loaderA:nth-child(5) {
  transform: rotate(80deg);
}
.loaderA:nth-child(6) {
  transform: rotate(100deg);
}
.loaderA:nth-child(7) {
  transform: rotate(120deg);
}
.loaderA:nth-child(8) {
  transform: rotate(140deg);
}
.loaderA:nth-child(9) {
  transform: rotate(160deg);
}

.ball1 {
  animation-delay: 0.2s;
}
.ball2 {
  animation-delay: 0.4s;
}
.ball3 {
  animation-delay: 0.6s;
}
.ball4 {
  animation-delay: 0.8s;
}
.ball5 {
  animation-delay: 1s;
}
.ball6 {
  animation-delay: 1.2s;
}
.ball7 {
  animation-delay: 1.4s;
}
.ball8 {
  animation-delay: 1.6s;
}
.ball9 {
  animation-delay: 1.8s;
}

@keyframes move {
  0% {
    transform: translateY(0em);
  }
  50% {
    transform: translateY(12em);
  }
  100% {
    transform: translateY(0em);
  }
}


