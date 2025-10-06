class GitTerminal {
    constructor() {
        this.lessonSlug = window.LESSON_SLUG;
        this.lesson = null;
        this.currentStep = 0;
        this.commandHistory = [];
        this.historyIndex = -1;
        this.isEditorMode = false;
        this.lineCounter = 0;
        
        this.terminalInput = document.getElementById('terminalInput');
        this.terminalOutput = document.getElementById('terminalOutput');
        this.terminalEditor = document.getElementById('terminalEditor');
        this.editorContent = document.getElementById('editorContent');
        this.progressFill = document.getElementById('progressFill');
        this.progressText = document.getElementById('progressText');
        this.stepTitle = document.getElementById('stepTitle');
        this.stepDescription = document.getElementById('stepDescription');
        this.allowedCommands = document.getElementById('allowedCommands');
        this.lessonComplete = document.getElementById('lessonComplete');
        
 
        console.log('DOM Elements found:', {
            terminalInput: !!this.terminalInput,
            terminalOutput: !!this.terminalOutput,
            progressFill: !!this.progressFill,
            progressText: !!this.progressText,
            stepTitle: !!this.stepTitle,
            stepDescription: !!this.stepDescription,
            allowedCommands: !!this.allowedCommands,
            lessonComplete: !!this.lessonComplete
        });
         
        this.init();
    }
    
    async init() {
        try {
            console.log('Initializing terminal for lesson:', this.lessonSlug);
            await this.loadLesson();
            console.log('Lesson loaded:', this.lesson);
            this.setupEventListeners();
            this.setupSwipeGestures();
            this.updateUI();
            this.focusInput();
            console.log('Terminal initialized successfully');
        } catch (error) {
            console.error('Failed to initialize terminal:', error);
            this.addTerminalLine('Error: Failed to load lesson data', 'error');
        }
    }
    
    async loadLesson() {
        const response = await fetch(`/data/${this.lessonSlug}`);
        if (!response.ok) {
            throw new Error(`Failed to load lesson: ${response.status}`);
        }
        this.lesson = await response.json();
    }
    
    setupEventListeners() {
        this.terminalInput.addEventListener('keydown', (e) => {
            switch (e.key) {
                case 'Enter':
                    e.preventDefault();
                    this.handleCommand();
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    this.navigateHistory(-1);
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    this.navigateHistory(1);
                    break;
            }
        });
        
        // Focus input when clicking anywhere in terminal (except output area for text selection)
        document.getElementById('terminal').addEventListener('click', (e) => {
            // Don't focus if clicking inside terminal-output (allow text selection)
            if (e.target.closest('.terminal-output')) {
                return;
            }
            this.focusInput();
        });
        
        // Restart lesson button
        const restartBtn = document.getElementById('restartLesson');
        if (restartBtn) {
            restartBtn.addEventListener('click', () => {
                this.restartLesson();
            });
        }
    }
    
    handleCommand() {
        const command = this.terminalInput.value.trim();
        if (!command) return;
        
        // Add command to history
        this.commandHistory.push(command);
        this.historyIndex = this.commandHistory.length;
        
        // Display the command in terminal (skip if in editor mode)
        if (!this.isEditorMode) {
            this.addTerminalLine(`$ ${command}`, 'command');
        }
        
        // Clear input
        this.terminalInput.value = '';
        
        // Process the command
        this.processCommand(command);
    }
    
    processCommand(command) {
        const currentStep = this.lesson.steps[this.currentStep];
        const allowedCommands = currentStep.allowedCommands || [];
        
        // Check if command is allowed
        let matchedCommandObj = null;
        let isAllowed = false;
        
        for (const allowedCmd of allowedCommands) {
            if (typeof allowedCmd === 'object' && allowedCmd.cmd && allowedCmd.valid) {
                const regex = new RegExp(allowedCmd.valid, 'i');
                if (regex.test(command)) {
                    isAllowed = true;
                    matchedCommandObj = allowedCmd;
                    break;
                }
            }
        }
        
        if (!isAllowed) {
            this.addTerminalLine(
                `Command not recognized or not allowed in this step: ${command}`, 
                'error'
            );
            this.addTerminalLine(
                'Hint: Check the allowed commands in the sidebar.', 
                'error'
            );
            return;
        }
        
        // Display output from the matched command object
        const outputs = matchedCommandObj?.output;

        if (!outputs || !Array.isArray(outputs) || outputs.length === 0) {
            // No output or empty array - add spacing only if no editor content
            if (!matchedCommandObj.editor) {
                this.addTerminalLine('', 'space');
            }
        } else {
            // Process each output item
            outputs.forEach((outputItem, index) => {
                // Validate output item structure
                if (typeof outputItem !== 'object' || outputItem === null) {
                    console.warn(`Invalid output element at index ${index}:`, outputItem);
                    return;
                }

                const text = outputItem.text;
                const hint = outputItem.hint;
                
                // Skip if neither text nor hint exists
                if (text === undefined && hint === undefined) {
                    console.warn(`Skipping output element at index ${index}: neither text nor hint provided`, outputItem);
                    return;
                }
                
                this.addTerminalLine(text, 'output', hint);
            });
        }
        
        // Check if command has editor content
        if (matchedCommandObj.editor) {
            this.showEditor(matchedCommandObj.editor);
        } else {
            this.hideEditor();
        }
        
        // Check if command has a 'next' property for jumping
        if (matchedCommandObj.next !== undefined) {
            this.jumpToStep(matchedCommandObj.next);
        } else {
            this.advanceStep();
        }
        
        // Check if we need to complete the lesson AFTER moving to next step
        const currentStepId = this.getCurrentStepId();
        const highestStepId = this.getHighestStepId();
        
        // Only complete if we're beyond the highest step (no more steps to show)
        if (this.currentStep >= this.lesson.steps.length) {
            this.addTerminalLine('🎉 Congratulations! You have completed this lesson!', 'success');
            this.showLessonCompletion();
        }
    }
    
    normalizeCommand(command) {
        // Remove extra spaces and normalize for comparison
        return command.replace(/\s+/g, ' ').trim();
    }
    
    addTerminalLine(text, type = 'output', hint = null) {
        const line = document.createElement('div');
        line.className = `terminal-line ${type}`;
        
        if (type === 'command') {
            line.innerHTML = `<span class="prompt">$ </span><span class="command-text">${this.escapeHtml(text.substring(2))}</span>`;
        } else if (type === 'output' || type === 'error' || type === 'success' || type === 'space') {
            line.textContent = (text ? text : ' ');
            
            // Add tooltip for output hint if available
            if (hint && type === 'output') {
                this.setupTooltip(line, hint);
                line.classList.add('has-hint');
            }
        }
        
        this.terminalOutput.appendChild(line);
        this.scrollToBottom();
    }
    
    scrollToBottom() {
        // Use smooth scrolling for a better user experience
        this.terminalOutput.scrollTop = this.terminalOutput.scrollHeight;        
    }
    
    advanceStep() {
        this.currentStep++;
        this.updateUI();
    }
    
    jumpToStep(targetStepId) {
        const stepIndex = this.findStepById(targetStepId);
        
        if (stepIndex !== -1) {
            this.currentStep = stepIndex;
        } else {
            // Fallback to sequential if step ID not found
            this.currentStep++;
        }
        this.updateUI();
    }
    
    showLessonCompletion() {
        // Update progress bar to show 100% completion
        if (this.progressFill) {
            this.progressFill.style.width = '100%';
        }
        if (this.progressText) {
            const highestStepId = this.getHighestStepId();
            this.progressText.textContent = `Completed all ${highestStepId} steps`;
        }
        
        // Clear the allowed commands list
        if (this.allowedCommands) {
            this.allowedCommands.innerHTML = '';
        }
        
        // Add action buttons to the terminal
        const buttonsDiv = document.createElement('div');
        buttonsDiv.className = 'terminal-line terminal-navigation';
        buttonsDiv.style.cssText = `
            margin-top: 1.5rem;
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        `;
        
        // Get navigation data
        const navigation = this.lesson.navigation;
        
        // Previous Lesson button (if exists)
        if (navigation && navigation.previous) {
            const prevButton = this.createNavigationButton(
                '← ' + navigation.previous.title,
                () => window.location.href = `/lesson/${navigation.previous.slug}`
            );
            buttonsDiv.appendChild(prevButton);
        }
        
        // All Lessons button
        const allButton = this.createNavigationButton(
            '🏠 All Lessons',
            () => window.location.href = '/'
        );
        buttonsDiv.appendChild(allButton);
        
        // Restart Lesson button
        const restartButton = this.createNavigationButton(
            '🔄 Restart',
            () => this.restartLesson()
        );
        buttonsDiv.appendChild(restartButton);
        
        // Next Lesson button (if exists)
        if (navigation && navigation.next) {
            const nextButton = this.createNavigationButton(
                navigation.next.title + ' →',
                () => window.location.href = `/lesson/${navigation.next.slug}`
            );
            buttonsDiv.appendChild(nextButton);
        }
        
        this.terminalOutput.appendChild(buttonsDiv);
        
        // Disable the input field
        this.terminalInput.disabled = true;
        this.terminalInput.placeholder = 'Lesson completed! Choose an option above.';
        
        // Scroll to bottom to show the buttons
        this.scrollToBottom();
    }
    
    hideAllTooltips() {
        document.querySelectorAll('.tooltip').forEach(tooltip => {
            tooltip.remove();
        });
    }
    
    updateUI() {
        // Clear all existing tooltips first
        this.hideAllTooltips();
        
        if (!this.lesson || !this.lesson.steps) {
            console.error('No lesson data available for UI update');
            return;
        }
        
        const totalSteps    = this.lesson.steps.length;
        const highestStepId = this.getHighestStepId();
        const currentStepId = this.getCurrentStepId();
        
        // Calculate progress based on step ID, not array position
        const progress = ((currentStepId - 1) / highestStepId) * 100;
        
        // Update progress bar
        if (this.progressFill) {
            this.progressFill.style.width = `${progress}%`;
        }
        if (this.progressText) {
            this.progressText.textContent = `Step ${currentStepId} of ${highestStepId}`;
        }
        
        // Update current step info - only if step exists
        const step = this.lesson.steps[this.currentStep];
        if (step) {
            if (this.stepTitle) {
                this.stepTitle.textContent = step.title;
            }
            if (this.stepDescription) {
                this.stepDescription.textContent = step.description;
            }
            
            // Update allowed commands
            if (this.allowedCommands) {
                this.allowedCommands.innerHTML = '';
                (step.allowedCommands || []).forEach(command => {
                    const li = document.createElement('li');
                    let commandText;
                    
                    // Handle both string and object formats
                    if (typeof command === 'string') {
                        commandText = command;
                        li.textContent = command;
                    } else if (typeof command === 'object' && command.cmd) {
                        commandText = command.cmd;
                        li.textContent = command.cmd;
                        
                        // Add tooltip for command hint if available
                        if (command.hint) {
                            this.setupTooltip(li, command.hint);
                        }
                    }
                    
                    // Create command text span
                    const commandSpan = document.createElement('span');
                    commandSpan.className = 'command-text';
                    commandSpan.textContent = li.textContent;
                    commandSpan.title = 'Click to fill input';
                    li.textContent = '';
                    li.appendChild(commandSpan);
                    
                    // Create clipboard icon (desktop only)
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        const clipboardIcon = document.createElement('span');
                        clipboardIcon.className = 'clipboard-icon';
                        clipboardIcon.innerHTML = '<svg class="bi" width="1.2em" height="1.2em"><use xlink:href="#clipboard"></use></svg>';
                        clipboardIcon.title = 'Copy to clipboard';
                        li.appendChild(clipboardIcon);
                        
                        // Click on clipboard icon - copy only
                        clipboardIcon.addEventListener('click', (e) => {
                            e.stopPropagation();
                            navigator.clipboard.writeText(commandText)
                                .then(() => {
                                    this.showCopyFeedback(clipboardIcon);
                                })
                                .catch(err => {
                                    console.warn('Failed to copy to clipboard:', err);
                                });
                        });
                    }
                    
                    // Click on command text - fill input only
                    commandSpan.addEventListener('click', () => {
                        this.fillCommand(commandText);
                    });
                    
                    this.allowedCommands.appendChild(li);
                });
            }
        }
    }
    
    restartLesson() {
        // Reset state
        this.currentStep = 0;
        this.commandHistory = [];
        this.historyIndex = -1;
        
        // Clear terminal output
        this.terminalOutput.innerHTML = `
            <div class="terminal-line">
                <span class="prompt">$ </span>
                <span>Type the commands shown in the sidebar to proceed.</span>
            </div>
        `;
        
        // Re-enable input field
        this.terminalInput.disabled = false;
        this.terminalInput.placeholder = 'Type your command here...';
        
        // Show terminal and hide completion message
        document.querySelector('.lesson-container').style.display = 'grid';
        this.lessonComplete.style.display = 'none';
        
        // Update UI
        this.updateUI();
        this.focusInput();
        this.scrollToBottom();
    }
    
    navigateHistory(direction) {
        if (this.commandHistory.length === 0) return;
        
        this.historyIndex += direction;
        
        if (this.historyIndex < 0) {
            this.historyIndex = 0;
        } else if (this.historyIndex >= this.commandHistory.length) {
            this.historyIndex = this.commandHistory.length;
            this.terminalInput.value = '';
            return;
        }
        
        this.terminalInput.value = this.commandHistory[this.historyIndex];
    }
    
    focusInput() {
        this.terminalInput.focus();
    }
    
    fillCommand(commandText) {
        if (this.terminalInput && !this.terminalInput.disabled) {
            this.terminalInput.value = commandText;
            this.terminalInput.focus();
            // Move cursor to the end
            this.terminalInput.setSelectionRange(commandText.length, commandText.length);
        }
    }
    
    showCopyFeedback(element) {
        // Temporarily change cursor to 'progress' for visual feedback
        const originalCursor = element.style.cursor;
        element.style.cursor = 'progress';
        
        // Restore original cursor after 500ms
        setTimeout(() => {
            element.style.cursor = originalCursor;
        }, 500);
    }
    
    findStepById(stepId) {
        return this.lesson.steps.findIndex(step => step.id === stepId);
    }
    
    getCurrentStepId() {
        const currentStep = this.lesson.steps[this.currentStep];
        return currentStep ? currentStep.id : this.getHighestStepId();
    }
    
    getHighestStepId() {
        return Math.max(...this.lesson.steps.map(step => step.id));
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    setupTooltip(element, hintText) {
        let tooltip = null;
        let showTimeout = null;
        let hideTimeout = null;
        
        element.addEventListener('mouseenter', (e) => {
            // Clear any existing hide timeout
            if (hideTimeout) {
                clearTimeout(hideTimeout);
                hideTimeout = null;
            }
            
            // Show tooltip after a short delay
            showTimeout = setTimeout(() => {
                tooltip = this.createTooltip(hintText);
                document.body.appendChild(tooltip);
                this.positionTooltip(tooltip, element);
                
                // Show tooltip with animation
                setTimeout(() => tooltip.classList.add('show'), 10);
            }, 70);
        });
        
        element.addEventListener('mouseleave', (e) => {
            // Clear show timeout if still pending
            if (showTimeout) {
                clearTimeout(showTimeout);
                showTimeout = null;
            }
            
            // Hide tooltip after a short delay
            if (tooltip) {
                tooltip.classList.remove('show');
                hideTimeout = setTimeout(() => {
                    if (tooltip && tooltip.parentNode) {
                        tooltip.parentNode.removeChild(tooltip);
                    }
                    tooltip = null;
                }, 100);
            }
        });
    }
    
    createTooltip(text) {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.innerHTML = text;
        return tooltip;
    }
    
    positionTooltip(tooltip, targetElement) {
        const rect = targetElement.getBoundingClientRect();
        const tooltipRect = tooltip.getBoundingClientRect();
        
        // Position tooltip above the element by default
        let top = rect.top - tooltipRect.height - 10;
        // Align tooltip to the left edge of the target element
        let left = rect.left;
        
        // Adjust if tooltip would go off screen
        if (top < 0) {
            // Position below if no space above
            top = rect.bottom + 10;
        }
        
        // Ensure tooltip doesn't go off the left edge
        if (left < 10) {
            left = 10;
        } 
        // Ensure tooltip doesn't go off the right edge
        else if (left + tooltipRect.width > window.innerWidth - 10) {
            left = window.innerWidth - tooltipRect.width - 10;
        }
        
        tooltip.style.top = `${top + window.scrollY}px`;
        tooltip.style.left = `${left}px`;
    }
    
    createNavigationButton(text, onClick) {
        const button = document.createElement('button');
        button.textContent = text;
        button.className = 'btn btn-primary';
        button.addEventListener('click', onClick);
        return button;
    }
    
    setupSwipeGestures() {
        let startX = 0;
        let startY = 0;
        let endX = 0;
        let endY = 0;
        
        const minSwipeDistance = 50;
        const maxVerticalDistance = 100;
        
        document.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        });
        
        document.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].clientX;
            endY = e.changedTouches[0].clientY;
            
            const deltaX = endX - startX;
            const deltaY = Math.abs(endY - startY);
            
            // Only trigger if horizontal swipe is significant and vertical movement is minimal
            if (Math.abs(deltaX) > minSwipeDistance && deltaY < maxVerticalDistance) {
                const navigation = this.lesson.navigation;
                
                if (deltaX > 0 && navigation && navigation.previous) {
                    // Swipe right - go to previous lesson
                    window.location.href = `/lesson/${navigation.previous.slug}`;
                } else if (deltaX < 0 && navigation && navigation.next) {
                    // Swipe left - go to next lesson
                    window.location.href = `/lesson/${navigation.next.slug}`;
                }
            }
        });
    }
    
    showEditor(editorData) {
        this.initEditor();
        
        // Handle editor content
        if (Array.isArray(editorData)) {
            editorData.forEach(element => {
                if (element.text !== undefined && element.text !== null) {
                    this.addEditorLine(element.text, element.hint);
                }
            });
        } else if (editorData.text) {
            this.addEditorLine(editorData.text, editorData.hint);
        }
    }

    initEditor() {
        if (this.terminalEditor && this.editorContent) {
            this.editorContent.innerHTML = '';
            this.terminalEditor.style.display = 'block';
            this.terminalOutput.style.display = 'none';
            this.isEditorMode = true;
            this.lineCounter = 0;
            document.body.classList.add('editor-mode');
        }
    }
    
    addEditorLine(text, hint = null) {
        if (this.editorContent) {
            const textContent = text ? text : ' ';
            const lineCount = Math.max(1, (textContent.match(/\n/g) || []).length);
            
            const editorLine = document.createElement('div');
            editorLine.className = 'editor-line';
            
            // Create line numbers column
            const lineNumbersContainer = document.createElement('div');
            lineNumbersContainer.className = 'editor-line-numbers';
            
            // Generate line numbers using the line counter
            const lineNumbers = [];
            for (let i = 0; i < lineCount; i++) {
                this.lineCounter++;
                lineNumbers.push(this.lineCounter);
            }
            const lineNumberText = lineNumbers.join('\n');
            
            const lineNumber = document.createElement('div');
            lineNumber.className = 'editor-line-number';
            lineNumber.textContent = lineNumberText;
            lineNumbersContainer.appendChild(lineNumber);
            
            // Create content area
            const content = document.createElement('div');
            content.className = 'editor-line-content';
            content.textContent = textContent;
            
            // Add tooltip for editor hint if available (same as terminal output)
            if (hint) {
                this.setupTooltip(editorLine, hint);
                editorLine.classList.add('has-hint');
            }
            
            editorLine.appendChild(lineNumbersContainer);
            editorLine.appendChild(content);
            this.editorContent.appendChild(editorLine);
        }
    }
    
    hideEditor() {
        if (this.terminalEditor) {
            this.terminalEditor.style.display = 'none';
            this.terminalOutput.style.display = 'block';
            this.isEditorMode = false;
            document.body.classList.remove('editor-mode');
            this.scrollToBottom();
        }
    }
}

// Initialize terminal when page loads
document.addEventListener('DOMContentLoaded', () => {
    if (window.LESSON_SLUG) {
        new GitTerminal();
    }
});

// Add some keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // Ctrl+L or Cmd+L to clear terminal (not implemented yet)
    if ((e.ctrlKey || e.metaKey) && e.key === 'l') {
        e.preventDefault();
        // Could implement clear terminal functionality here
    }
});

