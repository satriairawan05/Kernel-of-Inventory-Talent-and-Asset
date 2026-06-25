<div class="modal fade calc-modal" id="calcModal" tabindex="-1" aria-hidden="true" x-data="calculatorComponent">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title"><i class="bi bi-calculator me-2"></i>Calculator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="calc-card">
                    <div class="calc-display" id="calcDisplayModal" x-text="$store.pos.calcDisplay">0</div>
                    <div class="row g-2 calc-row mb-2">
                        <div class="col-6"><button class="calc-btn clr" @click="$store.pos.calcClear()">C</button></div>
                        <div class="col-3"><button class="calc-btn bksp" @click="$store.pos.calcBackspace()">⌫</button></div>
                        <div class="col-3"><button class="calc-btn op" @click="$store.pos.calcAppend('÷')">÷</button></div>
                    </div>
                    <div class="row g-2 calc-row mb-2">
                        <div class="col-3"><button class="calc-btn" @click="$store.pos.calcAppend('7')">7</button></div>
                        <div class="col-3"><button class="calc-btn" @click="$store.pos.calcAppend('8')">8</button></div>
                        <div class="col-3"><button class="calc-btn" @click="$store.pos.calcAppend('9')">9</button></div>
                        <div class="col-3"><button class="calc-btn op" @click="$store.pos.calcAppend('×')">×</button></div>
                    </div>
                    <div class="row g-2 calc-row mb-2">
                        <div class="col-3"><button class="calc-btn" @click="$store.pos.calcAppend('4')">4</button></div>
                        <div class="col-3"><button class="calc-btn" @click="$store.pos.calcAppend('5')">5</button></div>
                        <div class="col-3"><button class="calc-btn" @click="$store.pos.calcAppend('6')">6</button></div>
                        <div class="col-3"><button class="calc-btn op" @click="$store.pos.calcAppend('−')">−</button></div>
                    </div>
                    <div class="row g-2 calc-row mb-2">
                        <div class="col-3"><button class="calc-btn" @click="$store.pos.calcAppend('1')">1</button></div>
                        <div class="col-3"><button class="calc-btn" @click="$store.pos.calcAppend('2')">2</button></div>
                        <div class="col-3"><button class="calc-btn" @click="$store.pos.calcAppend('3')">3</button></div>
                        <div class="col-3"><button class="calc-btn op" @click="$store.pos.calcAppend('+')">+</button></div>
                    </div>
                    <div class="row g-2 calc-row">
                        <div class="col-6"><button class="calc-btn" @click="$store.pos.calcAppend('0')">0</button></div>
                        <div class="col-3"><button class="calc-btn" @click="$store.pos.calcAppend('.')">.</button></div>
                        <div class="col-3"><button class="calc-btn eq" @click="$store.pos.calcEvaluate()">=</button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>