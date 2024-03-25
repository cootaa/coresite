/**
 * 复制文本
 * @param {String} text 指定内容
 * @returns {Boolean} 是否复制成功
 */
export async function copyTextToClipboard(text: string) {
  try {
    await navigator.clipboard.writeText(text)
    return true
  } catch (err) {
    throw false
  }
}
