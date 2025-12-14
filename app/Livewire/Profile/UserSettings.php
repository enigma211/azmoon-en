<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UserSettings extends Component
{
    public $fontSize = 'medium';
    public $theme = 'light';

    public function mount()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->fontSize = $user->font_size ?? 'medium';
            $this->theme = $user->theme ?? 'light';
        }
    }

    public function updateFontSize($size)
    {
        $this->fontSize = $size;
        
        if (Auth::check()) {
            Auth::user()->update(['font_size' => $size]);
        }

        // Apply font size immediately using JavaScript with inline styles
        $fontSizes = [
            'small' => '14px',
            'medium' => '16px',
            'large' => '18px',
            'xlarge' => '20px'
        ];
        
        $fontSize = $fontSizes[$size] ?? '16px';
        
        $this->js("
            localStorage.setItem('userFontSize', '{$size}');
            document.body.style.fontSize = '{$fontSize}';
            document.body.setAttribute('data-font-size', '{$size}');
            if (typeof window.showNotification === 'function') {
                window.showNotification('اندازه فونت با موفقیت تغییر کرد');
            }
        ");
    }

    public function updateTheme($theme)
    {
        $this->theme = $theme;
        
        if (Auth::check()) {
            Auth::user()->update(['theme' => $theme]);
        }

        // Apply theme immediately
        $this->js("
            localStorage.setItem('userTheme', '{$theme}');
            document.body.setAttribute('data-theme', '{$theme}');
            
            if ('{$theme}' === 'dark') {
                document.body.style.backgroundColor = '#1f2937';
                document.body.style.color = '#f3f4f6';
                
                // برای صفحه آزمون
                setTimeout(() => {
                    document.querySelectorAll('.question-text, .choice-text, .exam-question').forEach(el => {
                        el.style.color = '#f3f4f6';
                    });
                    document.querySelectorAll('.text-gray-500, .text-gray-600').forEach(el => {
                        el.style.color = '#d1d5db';
                    });
                }, 100);
            } else {
                document.body.style.backgroundColor = '';
                document.body.style.color = '';
                
                setTimeout(() => {
                    document.querySelectorAll('.question-text, .choice-text, .exam-question').forEach(el => {
                        el.style.color = '';
                    });
                    document.querySelectorAll('.text-gray-500, .text-gray-600').forEach(el => {
                        el.style.color = '';
                    });
                }, 100);
            }
            
            if (typeof window.showNotification === 'function') {
                window.showNotification('تم با موفقیت تغییر کرد');
            }
        ");
    }

    public function render()
    {
        return view('livewire.profile.user-settings');
    }
}
