#!/usr/bin/env python3
"""
Windows Selenium Credential Tester - FINAL FIXED VERSION
Chrome 146+ desteği, Alert handling, Visible browser option
"""

import sys
import time
import os
import argparse
import shutil
from datetime import datetime

# Selenium
try:
    from selenium import webdriver
    from selenium.webdriver.chrome.service import Service
    from selenium.webdriver.common.by import By
    from selenium.webdriver.chrome.options import Options
    from selenium.common.exceptions import (
        TimeoutException, NoSuchElementException, 
        WebDriverException, UnexpectedAlertPresentException
    )
except ImportError:
    print("[!] Selenium kurulu değil!")
    print("[i] Kur: pip install selenium")
    sys.exit(1)


class LoginTester:
    """Ana test class'ı"""
    
    def __init__(self, success_file="successful_logins.txt", fail_file="failed_logins.txt", 
                 timeout=20, headless=True, chromedriver_path=None):
        self.success_file = success_file
        self.fail_file = fail_file
        self.timeout = timeout
        self.headless = headless
        self.chromedriver_path = chromedriver_path
        self.success_count = 0
        self.fail_count = 0
        self.driver = None
        
        # Logout desenleri
        self.logout_patterns = [
            'logout', 'log out', 'signout', 'sign out', 
            'exit', 'quit', 'çıkış', 'çık', 'odhlásit', 
            'déconnexion', 'salir', 'ausloggen', 'abmelden',
            'keluar', 'sortir', 'uscita'
        ]
        
        # Captcha/2FA desenleri
        self.captcha_patterns = [
            'captcha', 'recaptcha', 'g-recaptcha', 'hcaptcha',
            '2fa', 'two factor', 'two-factor', 'authentication code',
            'doğrulama kodu', 'verification code', 'sms kodu',
            'güvenlik kodu', 'security code', 'otp', 'code',
            'i\'m not a robot', 'ben robot değilim',
            'reCAPTCHA', 'grecaptcha', 'h-captcha'
        ]
    
    def find_chromedriver(self):
        """ChromeDriver'ı bul"""
        if self.chromedriver_path and os.path.exists(self.chromedriver_path):
            return self.chromedriver_path
        
        # Olası konumlar
        possible_paths = [
            'chromedriver.exe',
            r'.\chromedriver.exe',
            r'chromedriver-win64\chromedriver.exe',
            r'chromedriver-win32\chromedriver.exe',
        ]
        
        # PATH'ten kontrol et
        path_from_env = shutil.which('chromedriver')
        if path_from_env:
            possible_paths.insert(0, path_from_env)
        
        # Yaygın Windows konumları
        common_paths = [
            r'C:\WebDriver\chromedriver.exe',
            r'C:\Windows\System32\chromedriver.exe',
            r'C:\chromedriver\chromedriver.exe',
            r'C:\tools\chromedriver.exe',
        ]
        possible_paths.extend(common_paths)
        
        for path in possible_paths:
            if os.path.exists(path):
                print(f"[*] ChromeDriver bulundu: {path}")
                return path
        
        return None
    
    def init_driver(self):
        """Chrome'u başlat"""
        try:
            driver_path = self.find_chromedriver()
            if not driver_path:
                print("[!] ChromeDriver bulunamadı!")
                print("[i] İndir: https://chromedriver.chromium.org/downloads")
                print("[i] Chrome sürümünüz: 146.0.7680.80")
                print("[i] chromedriver.exe'yi bu klasöre kopyalayın")
                return False
            
            chrome_options = Options()
            
            # Headless mod
            if self.headless:
                chrome_options.add_argument('--headless=new')
            
            # Güvenlik ve stabilite ayarları
            chrome_options.add_argument('--no-sandbox')
            chrome_options.add_argument('--disable-dev-shm-usage')
            chrome_options.add_argument('--disable-gpu')
            chrome_options.add_argument('--window-size=1920,1080')
            chrome_options.add_argument('--disable-extensions')
            chrome_options.add_argument('--disable-setuid-sandbox')
            chrome_options.add_argument('--ignore-certificate-errors')
            chrome_options.add_argument('--ignore-ssl-errors')
            chrome_options.add_argument('--disable-blink-features=AutomationControlled')
            
            # User agent
            chrome_options.add_argument('--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36')
            
            # Resimleri devre dışı bırak
            prefs = {
                'profile.managed_default_content_settings.images': 2,
                'profile.default_content_setting_values.notifications': 2
            }
            chrome_options.add_experimental_option('prefs', prefs)
            
            service = Service(driver_path)
            self.driver = webdriver.Chrome(service=service, options=chrome_options)
            self.driver.set_page_load_timeout(self.timeout)
            
            print(f"[*] Chrome başlatıldı ✓")
            return True
            
        except Exception as e:
            print(f"[!] Chrome başlatma hatası: {e}")
            return False
    
    def close_driver(self):
        """Chrome'u kapat"""
        if self.driver:
            try:
                self.driver.quit()
            except:
                pass
            self.driver = None
    
    def handle_alert(self):
        """Alert/popup'ı kapat"""
        try:
            alert = self.driver.switch_to.alert
            text = alert.text
            alert.accept()
            return text
        except:
            return None
    
    def check_captcha(self, source):
        """Captcha/2FA kontrolü"""
        source_lower = source.lower()
        for pattern in self.captcha_patterns:
            if pattern in source_lower:
                return True, pattern
        return False, None
    
    def parse_line(self, line):
        """URL#USER@PASS parse et"""
        try:
            line = line.strip()
            if not line or line.startswith('#'):
                return None
            
            if '#' not in line or '@' not in line:
                return None
            
            url_part, cred_part = line.split('#', 1)
            username, password = cred_part.rsplit('@', 1)
            
            url = url_part.strip()
            if not url.startswith('http'):
                url = 'http://' + url
            
            return {'url': url, 'username': username.strip(), 'password': password.strip()}
        except:
            return None
    
    def save_result(self, result, is_success):
        """Sonucu kaydet"""
        try:
            if is_success:
                with open(self.success_file, 'a', encoding='utf-8') as f:
                    if f.tell() == 0:
                        f.write("="*70 + "\n")
                        f.write("✅ BAŞARILI GİRİŞLER\n")
                        f.write("="*70 + "\n\n")
                    
                    f.write(f"\n{'='*70}\n")
                    f.write(f"[✅] BAŞARILI\n")
                    f.write(f"{'='*70}\n")
                    f.write(f"Zaman: {result['timestamp']}\n")
                    f.write(f"URL: {result['url']}\n")
                    f.write(f"Kullanıcı: {result['username']}\n")
                    f.write(f"Şifre: {result['password']}\n")
                    f.write(f"Final URL: {result['final_url']}\n")
                    f.write(f"{'='*70}\n")
            else:
                with open(self.fail_file, 'a', encoding='utf-8') as f:
                    if f.tell() == 0:
                        f.write("="*70 + "\n")
                        f.write("❌ BAŞARISIZ GİRİŞLER\n")
                        f.write("="*70 + "\n\n")
                    
                    f.write(f"\n{'='*70}\n")
                    f.write(f"[❌] BAŞARISIZ\n")
                    f.write(f"{'='*70}\n")
                    f.write(f"Zaman: {result['timestamp']}\n")
                    f.write(f"URL: {result['url']}\n")
                    f.write(f"Kullanıcı: {result['username']}\n")
                    f.write(f"Şifre: {result['password']}\n")
                    
                    if result.get('reason'):
                        f.write(f"Neden: {', '.join(result['reason'])}\n")
                    if result.get('error'):
                        f.write(f"Hata: {result['error']}\n")
                    
                    f.write(f"{'='*70}\n")
        except Exception as e:
            print(f"[!] Kayıt hatası: {e}")
    
    def attempt_login(self, cred_info):
        """Giriş dene"""
        url = cred_info['url']
        username = cred_info['username']
        password = cred_info['password']
        
        print(f"\n{'='*70}")
        print(f"[*] Test: {url}")
        print(f"[*] Kullanıcı: {username}")
        print(f"[*] Şifre: {'*' * min(len(password), 10)}")
        print(f"{'='*70}")
        
        result = {
            'timestamp': datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
            'url': url,
            'username': username,
            'password': password,
            'success': False,
            'final_url': '',
            'error': None,
            'reason': []
        }
        
        try:
            # Driver başlat
            if not self.driver:
                if not self.init_driver():
                    result['error'] = 'Driver başlatılamadı'
                    self.save_result(result, False)
                    return result
            
            # Sayfaya git
            print(f"[*] Sayfa yükleniyor...")
            try:
                self.driver.get(url)
            except TimeoutException:
                result['error'] = 'Zaman aşımı'
                result['reason'].append('Sayfa yüklenemedi')
                self.save_result(result, False)
                return result
            
            time.sleep(3)
            
            # Alert kontrolü
            alert_text = self.handle_alert()
            if alert_text:
                print(f"[!] Alert: {alert_text}")
                result['error'] = f'Alert: {alert_text}'
                result['reason'].append(f'Alert: {alert_text}')
            
            # Captcha kontrolü
            if self.check_captcha(self.driver.page_source)[0]:
                print(f"[!] Captcha/2FA tespit edildi - Atlanıyor")
                result['error'] = 'Captcha/2FA'
                result['reason'].append('Captcha veya 2FA var')
                self.save_result(result, False)
                return result
            
            # Form bul
            print(f"[*] Form aranıyor...")
            
            # Şifre alanı
            pwd_field = None
            for sel in ["input[type='password']", "input[name*='pass']", "input[name*='pwd']"]:
                try:
                    pwd_field = self.driver.find_element(By.CSS_SELECTOR, sel)
                    break
                except:
                    continue
            
            if not pwd_field:
                print(f"[!] Form bulunamadı")
                result['error'] = 'Login formu bulunamadı'
                self.save_result(result, False)
                return result
            
            # Kullanıcı adı alanı
            user_field = None
            for sel in ["input[type='text']", "input[type='email']", "input[name*='user']", "input[name*='email']", "input[name*='id']"]:
                try:
                    user_field = self.driver.find_element(By.CSS_SELECTOR, sel)
                    break
                except:
                    continue
            
            print(f"[*] Form bulundu ✓")
            
            # Doldur
            if user_field:
                try:
                    user_field.clear()
                    user_field.send_keys(username)
                    time.sleep(0.3)
                except:
                    pass
            
            try:
                pwd_field.clear()
                pwd_field.send_keys(password)
                time.sleep(0.3)
            except:
                pass
            
            # Gönder
            print(f"[*] Giriş yapılıyor...")
            try:
                # Submit butonu ara
                submit_btn = None
                for sel in ["button[type='submit']", "input[type='submit']"]:
                    try:
                        submit_btn = self.driver.find_element(By.CSS_SELECTOR, sel)
                        break
                    except:
                        continue
                
                if submit_btn:
                    submit_btn.click()
                else:
                    pwd_field.submit()
                    
            except Exception as e:
                print(f"[!] Submit hatası: {e}")
            
            # Bekle
            time.sleep(4)
            
            # Alert kontrolü (giriş sonrası)
            alert_text = self.handle_alert()
            if alert_text:
                print(f"[!] Giriş sonrası alert: {alert_text}")
                result['error'] = f'Alert: {alert_text}'
                result['reason'].append(f'Site alert gösterdi: {alert_text}')
                self.save_result(result, False)
                return result
            
            # Sonucu analiz et
            result['final_url'] = self.driver.current_url
            final_src = self.driver.page_source.lower()
            final_url = result['final_url'].lower()
            
            print(f"[*] Final URL: {result['final_url']}")
            
            # Logout var mı?
            has_logout = any(p in final_src or p in final_url for p in self.logout_patterns)
            
            # Login sayfasında mı?
            on_login = any(k in final_url for k in ['login', 'signin'])
            
            # Skor
            score = 0
            if has_logout: score += 10
            if not on_login: score += 5
            
            print(f"\n[*] Analiz:")
            print(f"    - Logout: {'✓' if has_logout else '✗'}")
            print(f"    - Login sayfası: {'✓ (KÖTÜ)' if on_login else '✗ (İYİ)'}")
            print(f"    - Skor: {score}/15")
            
            # Başarılı mı?
            is_success = has_logout and not on_login
            result['success'] = is_success
            
            if is_success:
                self.success_count += 1
                print(f"\n[✅] BAŞARILI!")
            else:
                self.fail_count += 1
                reason = []
                if on_login:
                    reason.append("Hala login sayfasında")
                if not has_logout:
                    reason.append("Logout yok")
                result['reason'] = reason
                print(f"\n[❌] BAŞARISIZ: {', '.join(reason)}")
            
            self.save_result(result, is_success)
            return result
            
        except UnexpectedAlertPresentException:
            # Beklenmeyen alert
            alert_text = self.handle_alert()
            print(f"[!] Beklenmeyen alert: {alert_text}")
            result['error'] = f'Alert: {alert_text}'
            result['reason'].append(f'Alert: {alert_text}')
            self.save_result(result, False)
            return result
            
        except Exception as e:
            print(f"[!] Hata: {e}")
            result['error'] = str(e)
            self.save_result(result, False)
            return result
    
    def run_from_file(self, filename):
        """Dosyadan oku ve test et"""
        try:
            with open(filename, 'r', encoding='utf-8') as f:
                lines = [l.strip() for l in f if l.strip() and not l.startswith('#')]
        except Exception as e:
            print(f"[!] Dosya hatası: {e}")
            return
        
        print(f"\n{'='*70}")
        print(f"[*] Selenium Login Tester")
        print(f"[*] Toplam: {len(lines)} site")
        print(f"[*] Başarılı: {self.success_file}")
        print(f"[*] Başarısız: {self.fail_file}")
        print(f"[*] Mod: {'Görünür' if not self.headless else 'Headless'}")
        print(f"{'='*70}\n")
        
        if not self.init_driver():
            print("[!] Başlatılamadı")
            return
        
        try:
            for i, line in enumerate(lines, 1):
                print(f"\n[{i}/{len(lines)}]")
                
                cred = self.parse_line(line)
                if cred:
                    self.attempt_login(cred)
                else:
                    print(f"[!] Geçersiz format: {line}")
                
                if i < len(lines):
                    time.sleep(1.5)
                    
        except KeyboardInterrupt:
            print("\n[!] Durduruldu")
        finally:
            self.close_driver()
            print(f"\n{'='*70}")
            print(f"[✅] Başarılı: {self.success_count}")
            print(f"[❌] Başarısız: {self.fail_count}")
            print(f"{'='*70}\n")


def main():
    parser = argparse.ArgumentParser(
        description='Selenium Login Tester',
        epilog="""
Örnekler:
  python login_tester_final.py list.txt
  python login_tester_final.py list.txt --visible
  python login_tester_final.py list.txt --timeout 30

ChromeDriver:
  https://chromedriver.chromium.org/downloads
  chromedriver.exe'yi kodun yanına kopyala
        """
    )
    
    parser.add_argument('file', help='Test listesi (URL#USER@PASS)')
    parser.add_argument('-s', '--success', default='successful_logins.txt', help='Başarılı dosyası')
    parser.add_argument('-f', '--fail', default='failed_logins.txt', help='Başarısız dosyası')
    parser.add_argument('--timeout', type=int, default=20, help='Zaman aşımı (saniye)')
    parser.add_argument('--visible', action='store_true', help='Tarayıcıyı göster')
    parser.add_argument('--driver', help='ChromeDriver.exe yolu')
    
    args = parser.parse_args()
    
    # Class adı DÜZELTİLDİ: LoginTester
    tester = LoginTester(
        success_file=args.success,
        fail_file=args.fail,
        timeout=args.timeout,
        headless=not args.visible,
        chromedriver_path=args.driver
    )
    
    tester.run_from_file(args.file)


if __name__ == '__main__':
    main()
