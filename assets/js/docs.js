// STCMS - Documentation JavaScript

document.addEventListener('DOMContentLoaded', function() {
    
    // Table of contents generation
    const generateTOC = () => {
        const content = document.querySelector('.docs-body');
        const headings = content.querySelectorAll('h2, h3, h4');
        const toc = document.createElement('nav');
        toc.className = 'table-of-contents';
        toc.innerHTML = '<h4>Table of Contents</h4><ul></ul>';
        
        const tocList = toc.querySelector('ul');
        let currentH2 = null;
        let currentH3 = null;
        
        headings.forEach((heading, index) => {
            const id = heading.textContent.toLowerCase().replace(/[^a-z0-9]+/g, '-');
            heading.id = id;
            
            const link = document.createElement('a');
            link.href = `#${id}`;
            link.textContent = heading.textContent;
            
            const listItem = document.createElement('li');
            listItem.appendChild(link);
            
            if (heading.tagName === 'H2') {
                currentH2 = listItem;
                currentH3 = null;
                tocList.appendChild(listItem);
            } else if (heading.tagName === 'H3') {
                if (!currentH2.querySelector('ul')) {
                    const subList = document.createElement('ul');
                    currentH2.appendChild(subList);
                }
                currentH3 = listItem;
                currentH2.querySelector('ul').appendChild(listItem);
            } else if (heading.tagName === 'H4') {
                if (currentH3 && !currentH3.querySelector('ul')) {
                    const subList = document.createElement('ul');
                    currentH3.appendChild(subList);
                }
                if (currentH3) {
                    currentH3.querySelector('ul').appendChild(listItem);
                }
            }
        });
        
        if (tocList.children.length > 0) {
            const docsContent = document.querySelector('.docs-content');
            docsContent.insertBefore(toc, docsContent.firstChild);
        }
    };
    
    // Generate table of contents if there are headings
    if (document.querySelector('.docs-body h2, .docs-body h3, .docs-body h4')) {
        generateTOC();
    }
    
    // Syntax highlighting for code blocks
    const highlightCode = () => {
        const codeBlocks = document.querySelectorAll('pre code');
        codeBlocks.forEach(block => {
            // Simple syntax highlighting for common keywords
            let code = block.textContent;
            
            // PHP highlighting
            if (code.includes('<?php') || code.includes('<?=')) {
                code = code
                    .replace(/\b(<?php|<?=)\b/g, '<span class="php-tag">$1</span>')
                    .replace(/\b(function|class|public|private|protected|static|const|return|if|else|foreach|while|for|switch|case|break|continue|new|extends|implements|interface|abstract|final|namespace|use|as|require|include|require_once|include_once)\b/g, '<span class="keyword">$1</span>')
                    .replace(/\b(true|false|null)\b/g, '<span class="constant">$1</span>')
                    .replace(/\b(\$[a-zA-Z_][a-zA-Z0-9_]*)\b/g, '<span class="variable">$1</span>')
                    .replace(/\b([0-9]+)\b/g, '<span class="number">$1</span>')
                    .replace(/\b(".*?"|'.*?')\b/g, '<span class="string">$1</span>');
            }
            
            // HTML highlighting
            else if (code.includes('<') && code.includes('>')) {
                code = code
                    .replace(/(&lt;\/?)([a-zA-Z][a-zA-Z0-9]*)([^&]*?)(&gt;)/g, '<span class="tag">$1$2$3$4</span>')
                    .replace(/\b(class|id|href|src|alt|title|type|name|value|required|disabled|readonly|checked|selected)\b/g, '<span class="attribute">$1</span>');
            }
            
            // CSS highlighting
            else if (code.includes('{') && code.includes('}')) {
                code = code
                    .replace(/([a-zA-Z-]+)(\s*:\s*)/g, '<span class="property">$1</span>$2')
                    .replace(/(:\s*)([^;]+)(;)/g, '$1<span class="value">$2</span>$3')
                    .replace(/([.#][a-zA-Z][a-zA-Z0-9_-]*)/g, '<span class="selector">$1</span>');
            }
            
            block.innerHTML = code;
        });
    };
    
    highlightCode();
    
    // Add CSS for syntax highlighting
    const style = document.createElement('style');
    style.textContent = `
        .php-tag { color: #dc2626; }
        .keyword { color: #2563eb; font-weight: bold; }
        .constant { color: #059669; }
        .variable { color: #7c3aed; }
        .number { color: #dc2626; }
        .string { color: #059669; }
        .tag { color: #dc2626; }
        .attribute { color: #2563eb; }
        .property { color: #2563eb; }
        .value { color: #059669; }
        .selector { color: #7c3aed; }
    `;
    document.head.appendChild(style);
    
    // Search functionality for documentation
    const createSearchBox = () => {
        const searchContainer = document.createElement('div');
        searchContainer.className = 'docs-search';
        searchContainer.innerHTML = `
            <input type="search" placeholder="Search documentation..." class="search-input">
            <div class="search-results"></div>
        `;
        
        const sidebar = document.querySelector('.docs-sidebar');
        if (sidebar) {
            sidebar.insertBefore(searchContainer, sidebar.firstChild);
        }
        
        const searchInput = searchContainer.querySelector('.search-input');
        const searchResults = searchContainer.querySelector('.search-results');
        
        const searchContent = () => {
            const query = searchInput.value.toLowerCase();
            const content = document.querySelector('.docs-body');
            const headings = content.querySelectorAll('h2, h3, h4, p');
            
            searchResults.innerHTML = '';
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }
            
            const results = [];
            headings.forEach(element => {
                const text = element.textContent.toLowerCase();
                if (text.includes(query)) {
                    const excerpt = element.textContent.substring(0, 100) + '...';
                    results.push({
                        text: element.textContent,
                        excerpt: excerpt,
                        element: element
                    });
                }
            });
            
            if (results.length > 0) {
                const resultsList = document.createElement('ul');
                results.slice(0, 5).forEach(result => {
                    const li = document.createElement('li');
                    li.innerHTML = `
                        <a href="#${result.element.id || ''}" class="search-result-link">
                            <strong>${result.text}</strong>
                            <br><small>${result.excerpt}</small>
                        </a>
                    `;
                    resultsList.appendChild(li);
                });
                searchResults.appendChild(resultsList);
                searchResults.style.display = 'block';
            } else {
                searchResults.innerHTML = '<p>No results found</p>';
                searchResults.style.display = 'block';
            }
        };
        
        searchInput.addEventListener('input', STCMS.debounce(searchContent, 300));
        
        // Hide search results when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchContainer.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    };
    
    createSearchBox();
    
    // Add CSS for search
    const searchStyle = document.createElement('style');
    searchStyle.textContent = `
        .docs-search {
            padding: 1rem 2rem;
            border-bottom: 1px solid #e5e5e5;
        }
        
        .search-input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 0.875rem;
        }
        
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-top: none;
            max-height: 300px;
            overflow-y: auto;
            display: none;
            z-index: 1000;
        }
        
        .search-results ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .search-results li {
            border-bottom: 1px solid #f3f4f6;
        }
        
        .search-result-link {
            display: block;
            padding: 0.75rem 1rem;
            color: #374151;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        
        .search-result-link:hover {
            background-color: #f9fafb;
        }
        
        .search-result-link strong {
            color: #1f2937;
        }
        
        .search-result-link small {
            color: #6b7280;
            font-size: 0.75rem;
        }
    `;
    document.head.appendChild(searchStyle);
    
    // Progress indicator for reading
    const createProgressBar = () => {
        const progressBar = document.createElement('div');
        progressBar.className = 'reading-progress';
        progressBar.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: #2563eb;
            z-index: 1001;
            transition: width 0.1s ease;
        `;
        document.body.appendChild(progressBar);
        
        const updateProgress = () => {
            const content = document.querySelector('.docs-body');
            if (content) {
                const contentHeight = content.offsetHeight;
                const scrollTop = window.pageYOffset;
                const windowHeight = window.innerHeight;
                const progress = Math.min((scrollTop / (contentHeight - windowHeight)) * 100, 100);
                progressBar.style.width = progress + '%';
            }
        };
        
        window.addEventListener('scroll', STCMS.throttle(updateProgress, 100));
    };
    
    createProgressBar();
    
    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        // Ctrl/Cmd + K for search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.focus();
            }
        }
        
        // Escape to close search results
        if (e.key === 'Escape') {
            const searchResults = document.querySelector('.search-results');
            if (searchResults) {
                searchResults.style.display = 'none';
            }
        }
    });
    
    // Print styles
    const printStyle = document.createElement('style');
    printStyle.textContent = `
        @media print {
            .header, .footer, .docs-sidebar, .search-container, .scroll-to-top {
                display: none !important;
            }
            
            .docs-content {
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .docs-body {
                font-size: 12pt;
                line-height: 1.4;
            }
            
            .code-block {
                background: #f8f9fa !important;
                color: #000 !important;
                border: 1px solid #dee2e6;
            }
        }
    `;
    document.head.appendChild(printStyle);
    
    console.log('STCMS Documentation enhanced! 📚');
}); 