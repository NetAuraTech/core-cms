type Props = {
  name: string
  size?: number
  additionalClass?: string
  path?: string
}

export default function Icon({
  name,
  size,
  path = 'vendor/core-cms/sprite.svg',
  additionalClass,
}: Props) {
  const className = `icon icon-${name} ${additionalClass}`
  const href = `/${path}#${name}`
  return (
    <svg className={className} width={size} height={size}>
      <use xlinkHref={href} />
    </svg>
  )
}
